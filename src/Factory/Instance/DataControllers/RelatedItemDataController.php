<?php

namespace Lkt\Factory\Instance\DataControllers;

use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Instantiator\Helpers\QueryBuilderHelper;
use Lkt\Factory\Instantiator\Instantiator;
use Lkt\Factory\Schemas\Schema;
use Lkt\QueryBuilding\Where;

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

    public function getQuery(
        string     $key,
        Where|null $where = null,
        int|null   $page = null,
        int|null   $itemsPerPage = null,
        array      $additionalData = [],
        bool       $forceRefresh = false
    )
    {
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
        $fieldConfigWhere = $field->getWhere();
        if ($fieldConfigWhere) {
            if (is_array($fieldConfigWhere)) {
                foreach ($fieldConfigWhere as $w) {
                    $builder->andWhere($w);
                }
            } else {
                $builder->andWhere($fieldConfigWhere);
            }
        }
        $builder->andPageLimitIs(1);
        return $builder;
    }

    public function getItem(string $key, array $additionalData, bool $retrieveAnonymous = false): Item|null
    {
        if (array_key_exists($key, $this->data)) return $this->data[$key];

        $field = $this->schema->getRelatedField($key);
        if (!$field) return null;

        if (array_key_exists($key, $this->data)) return $this->data[$key];

        $relatedSchema = Schema::get($field->getComponent());

        if ($this->item->isAnonymous()) {
            if ($retrieveAnonymous) {
                $instance = $relatedSchema->getItemInstance();
                $instance->feed($additionalData);
                $this->data[$key] = $instance;
                return $this->data[$key];
            }

            return null;
        }

        $builder = $this->getQuery($key);

        $results = $relatedSchema->getMany($builder);
        if (count($results) > 0) {
            $this->data[$key] = $results[0];
            return $this->data[$key];
        }

        // Related mode, should return an anonymous instance
        if (count($additionalData) > 0) {
            $instance = $relatedSchema->getItemInstance();
            $instance->feed($additionalData);
            $this->data[$key] = $instance;
            return $this->data[$key];
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

            if (!$instance->hasAssignedValue($relatedFieldPointingMeKey)) {
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