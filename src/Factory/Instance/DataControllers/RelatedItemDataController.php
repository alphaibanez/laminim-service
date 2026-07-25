<?php

namespace Lkt\Factory\Instance\DataControllers;

use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Instantiator\Helpers\QueryBuilderHelper;
use Lkt\Factory\Instantiator\Instances\AbstractInstance;
use Lkt\Factory\Instantiator\Instantiator;
use Lkt\Factory\Schemas\Schema;

final class RelatedItemDataController
{
    private array $data = [];
    private array $payload = [];
    private array $needsUpdate = [];

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
            $instance->feed($additionalData);
            return $instance;
        }

        return null;
    }

    public function has(string $key, array $additionalData = []): bool
    {
        return $this->getItem($key, $additionalData) !== null;
    }



    public function setItem(string $key, array $item, string $accessPolicy = 'lkt-related')
    {
        $field = $this->schema->getKindOfRelatedField($key);
        if (!$field) return null;

        $accessPolicyUsage = $this->item->getAccessPolicyUsage();

        if ($accessPolicyUsage) {
            $customRelationAccessPolicy = $field->getAssociatedAccessPolicy($accessPolicyUsage->name);
            if ($customRelationAccessPolicy) $accessPolicy = $customRelationAccessPolicy;
        }

        $relatedComponent = $field->getComponent($this->schema, $this->item);
        if ($relatedComponent === '') return null;

        $relatedSchema = Schema::get($relatedComponent);
        $relatedClass = $relatedSchema->getInstanceSettings()->getAppClass();

        $relatedFieldPointingMe = $relatedSchema->getField($field->getColumn());
        $relatedFieldPointingMeKey = $relatedFieldPointingMe->getName();

        $instance = null;

        if (is_array($item)) {
            $instance = Instantiator::make($relatedComponent, $item);

        } elseif ($item instanceof $relatedClass) {
            $instance = $item;
        }

        if ($instance instanceof  $relatedClass) {
            if ($accessPolicy) $instance->setAccessPolicy($accessPolicy);
            $instance->feed($item);

            if (true || !$instance->hasAssignedValue($relatedFieldPointingMeKey)) {
                $instance->assignValue($relatedFieldPointingMeKey, $this->item->getIdColumnValue());
            }
        }

        // @remember Changes won't be persistent until $this->item was saved!
        $this->needsUpdate[$key] = $instance;
        $this->payload[$key] = $item;
    }

    public function save(): self
    {
        /**
         * @var string $key
         * @var Item[] $items
         */
        foreach ($this->needsUpdate as $key => $item) $item->save();

        $this->needsUpdate = [];
        $this->payload = [];

        return $this;
    }

    public function hasToSave(): bool
    {
        return count($this->needsUpdate) > 0;
    }

    public function __debugInfo() {
        return [
            'data' => $this->data,
            'needsUpdate' => $this->needsUpdate,
        ];
    }
}