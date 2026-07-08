<?php

namespace Lkt\Factory\Instance\DataControllers;

use Lkt\Debug\VarDumper;
use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Instantiator\Helpers\QueryBuilderHelper;
use Lkt\Factory\Instantiator\Instantiator;
use Lkt\Factory\Schemas\Schema;

final class RelatedItemDataController
{
    private array $data = [];
    private array $payload = [];

    private Schema $schema;
    private Item $item;

    public function __construct(Schema &$schema, Item &$ins, array $data)
    {
        $this->schema = $schema;
        $this->item = $ins;
    }

    public function getItem(string $key, array $additionalData): Item|null
    {
        if ($this->item->isAnonymous()) return null;
        if (array_key_exists($key, $this->data)) return $this->data[$key];

        $field = $this->schema->getRelatedField($key);
        if (!$field) return null;

        $builder = QueryBuilderHelper::prepareRelatedQuery(
            $this->item,
            QueryBuilderHelper::getComponentQuery($field->getComponent()),
            $this->schema,
            $field,
            false,
            $additionalData
        );
        $builder->andPageLimitIs(1);

        $data = $builder->select();
        $relatedSchema = Schema::get($field->getComponent());

        $results = Instantiator::makeResults($relatedSchema->getComponent(), $data);
        if (count($results) > 0) {
            $this->data[$key] = $results[0];
            return $this->data[$key];
        }

        // Related mode, should return an anonymous instance
        if (count($additionalData) > 0) {
            $relatedSchema = Schema::get($field->getComponent($this->schema, $this->item));
            $instance = $relatedSchema->getItemInstance();
            $instance->initialFeed($additionalData);
            return $instance;
        }

        return null;
    }

    public function has(string $key): bool
    {
        return $this->getItem($key) !== null;
    }

    public function __debugInfo() {
        return [
            'data' => $this->data,
        ];
    }
}