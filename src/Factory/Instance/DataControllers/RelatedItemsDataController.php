<?php

namespace Lkt\Factory\Instance\DataControllers;

use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Instantiator\Helpers\QueryBuilderHelper;
use Lkt\Factory\Instantiator\Instances\AbstractInstance;
use Lkt\Factory\Instantiator\Instantiator;
use Lkt\Factory\Schemas\Fields\RelatedKeysField;
use Lkt\Factory\Schemas\Schema;
use Lkt\QueryBuilding\Where;
use function Lkt\Tools\Arrays\compareArrays;
use function Lkt\Tools\Pagination\getTotalPages;

final class RelatedItemsDataController
{
    private array $data = [];
    private array $payload = [];
    private array $needsUpdate = [];
    private array $itemsCount = [];
    private array $itemsAmountOfPages = [];

    private array $appendItemsInParentForeignKeysFieldStack = [];

    private Schema $schema;
    private Item $item;

    public function __construct(Schema &$schema, Item &$ins, array $data)
    {
        $this->schema = $schema;
        $this->item = $ins;
    }

    public function has(string $key): bool
    {
        $v = $this->getItems($key);
        return is_array($v) && count($v) > 0;
    }

    public function getItemsIdentifiers(
        string     $key,
        Where|null $where = null,
        int|null   $page = null,
        int|null   $itemsPerPage = null,
        array      $additionalData = [],
        bool       $forceRefresh = false
    ): array|null
    {
        $items = $this->getItems($key, $where, $page, $itemsPerPage, $additionalData, $forceRefresh);
        if (!$items) return [];

        return array_map(function (Item $item) {
            return $item->getIdentifierValue();
        }, $items);
    }

    public function getItemsIds(
        string     $key,
        Where|null $where = null,
        int|null   $page = null,
        int|null   $itemsPerPage = null,
        array      $additionalData = [],
        bool       $forceRefresh = false
    ): array|null
    {
        $ids = $this->getItemsIdentifiers($key, $where, $page, $itemsPerPage, $additionalData, $forceRefresh);
        if (!$ids) return [];

        if (count($ids[0]) === 1) {
            $k = array_keys($ids[0])[0];
            $ids = array_map(function (array $id) use ($k) {
                return $id[$k];
            }, $ids);
        }

        return $ids;
    }

    /**
     * @param string $key
     * @param Where|null $where
     * @param int|null $page
     * @param int|null $itemsPerPage
     * @param array $additionalData
     * @param bool $forceRefresh
     * @return Item[]|null
     * @throws \Lkt\Factory\Schemas\Exceptions\InvalidComponentException
     * @throws \Lkt\Factory\Schemas\Exceptions\InvalidSchemaAppClassException
     * @throws \Lkt\Factory\Schemas\Exceptions\SchemaNotDefinedException
     */
    public function getItems(
        string     $key,
        Where|null $where = null,
        int|null   $page = null,
        int|null   $itemsPerPage = null,
        array      $additionalData = [],
        bool       $forceRefresh = false
    ): array|null
    {
        if ($this->item->isAnonymous()) return null;

        $cacheKey = [$key];
        if ($page !== null) $cacheKey[] = $page;
        $cacheKey = implode('-', $cacheKey);

        if (!$forceRefresh) {
            if (array_key_exists($cacheKey, $this->payload)) return $this->payload[$cacheKey];
            if (array_key_exists($cacheKey, $this->data)) return $this->data[$cacheKey];
        }

        $field = $this->schema->getKindOfRelatedField($key);
        if (!$field) return null;

        $builder = $this->getQuery($key, $where, $page, $itemsPerPage, $additionalData);

        $data = $builder->select();
        $relatedSchema = Schema::get($field->getComponent());

        $results = Instantiator::makeResults($relatedSchema->getComponent(), $data);
        if (count($results) > 0) {
            $this->data[$cacheKey] = $results;
            return $this->data[$cacheKey];
        }
        return null;
    }

    public function getItemsCount(
        string      $key,
        Where|null  $where = null,
        string|null $countableField = null
    ): int|null
    {
        if (array_key_exists($key, $this->itemsCount)) return $this->itemsCount[$key];

        $field = $this->schema->getKindOfRelatedField($key);
        if (!$field) return null;

        if (!$countableField) {
            $countableField = $field->getCountableField();
        }

        if (!$countableField) {
            $relatedSchema = Schema::get($field->getComponent());
            $identifiers = $relatedSchema->getIdentifiers();
            if (count($identifiers) === 1) {
                $countableField = $identifiers[0]->getColumn();
            }
        }

        $builder = $this->getQuery($key, $where);

        $this->itemsCount[$key] = $builder->count($countableField);
        return $this->itemsCount[$key];
    }

    public function getItemsAmountOfPages(
        string      $key,
        Where|null  $where = null,
        string|null $countableField = null,
        int|null    $itemsPerPage = null
    ): int|null
    {
        if (array_key_exists($key, $this->itemsAmountOfPages)) return $this->itemsAmountOfPages[$key];

        $field = $this->schema->getKindOfRelatedField($key);
        if (!$field) return null;

        $total = $this->getItemsCount($key, $where, $countableField);
        $limit = ($itemsPerPage ?? $field->getItemsPerPage()) ?? 10;

        $this->itemsAmountOfPages[$key] = getTotalPages($total, $limit);
        return $this->itemsAmountOfPages[$key];
    }

    public function __debugInfo()
    {
        return [
            'data' => $this->data,
        ];
    }

    public function setItems(string $key, array $items, string $accessPolicy = 'lkt-related')
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
        $relatedIdentifiers = $relatedSchema->getIdentifiers();

        $itemsWithFedData = [];
        foreach ($this->data as &$datum) {
            if (is_array($datum)) {

                $constructorData = [];

                foreach ($relatedIdentifiers as $relatedIdentifier) {
                    $name = $relatedIdentifier->getName();
                    // @todo detect $this->item applied access policy
                    // and change the name var in order of avoid missing refs
                    // in case a field name was overwritten
                    if (!$datum[$name]) {
                        if (method_exists($field, 'getRelatedComponentFeeds')) {
                            foreach ($field->getRelatedComponentFeeds() as $relatedColumnKey => $relatedColumnValue) {
                                if (is_callable($relatedColumnValue)) {
                                    $relatedColumnValue = call_user_func_array($relatedColumnValue, [
                                        'referrer' => $this->item
                                    ]);
                                }
                                if (!$datum[$relatedColumnKey]) $datum[$relatedColumnKey] = $relatedColumnValue;
                            }
                        }
                    }

                    $constructorData[$name] = $datum[$name];
                }

                /** @var Item $instance */
                $instance = call_user_func_array([$relatedClass, 'getInstance'], $constructorData);
                if ($accessPolicy) $instance->setAccessPolicy($accessPolicy);
                $instance::feedInstance($instance, $datum);

            } else if (is_numeric($datum)) {
                $instance = call_user_func_array([$relatedClass, 'getInstance'], [$datum]);

            }

            if ($instance) {
                $itemsWithFedData[] = $instance;
            }
        }

        // @remember Changes won't be persistent until $this->item was saved!
        $this->needsUpdate[$key] = $itemsWithFedData;
        $this->payload[$key] = $items;
    }

    public function save(): self
    {
        /**
         * @var string $key
         * @var array[] $items
         */
        foreach ($this->needsUpdate as $key => $items) {

            $field = $this->schema->getKindOfRelatedField($key);

            $relatedComponent = $field->getComponent($this->schema, $this->item);
            $relatedSchema = Schema::get($relatedComponent);
            /** @var AbstractInstance $relatedClass */
            $relatedClass = $relatedSchema->getInstanceSettings()->getAppClass();

            $currentIds = $this->getItemsIds($key, null, null, null, [], true);
            $updatedIds = [];

            $updatedInstances = [];

            foreach ($items as $item) {
                /** @var Item $ins */
                $ins = is_array($item) ? $relatedClass::getInstance($item) : $item;
                $ins->feed($item);
                $updatedInstances[] = $ins;
                $updatedIds[] = $ins->getIdColumnValue();
            }

            if (count($updatedInstances) > 0) {
                $batchActions = $relatedClass::getBatchActions($updatedInstances);
                $batchActions->update();
            }

            $diff = compareArrays($currentIds, $updatedIds);

            // Delete
            if (count($diff['deleted']) > 0 && method_exists($field, 'hasToAutoRemoveUnlinked') && $field->hasToAutoRemoveUnlinked()) {
                foreach ($diff['deleted'] as $deletedId) {
                    $ins = $relatedClass::getInstance($relatedSchema->decodeInstanceCode($deletedId));
                    $ins->delete();
                }
            }


        }

        return $this;
    }

    public function prepareToAppendItemsInParentForeignKeysField(string $key, int|array $parentIdentifierValue): self
    {
        $this->appendItemsInParentForeignKeysFieldStack[$key] = $parentIdentifierValue;
        return $this;
    }

    public function appendItemsInParentForeignKeysField(string $key): self
    {
        $field = $this->schema->getRelatedKeysField($key);
        if (!$field) return $this;


        $relatedId = $this->appendItemsInParentForeignKeysFieldStack[$key];
        if (!is_array($relatedId)) {
            $relatedId = [$relatedId];
        }

        // @todo

        return $this;
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
        $field = $this->schema->getKindOfRelatedField($key);
        if (!$field) return null;

        $builder = QueryBuilderHelper::prepareRelatedQuery(
            $this->item,
            QueryBuilderHelper::getComponentQuery($field->getComponent()),
            $this->schema,
            $field,
            $forceRefresh,
            $additionalData,
        );

        if ($field instanceof RelatedKeysField) {
            $constraints = $this->item::getWhereBuilder();
            foreach ($this->item->getIdentifierValue() as $column => $value) {
                $constraints->andWhere(
                    $this->item::getWhereBuilder()
                        ->orStringLike($column, ";{$value};")
                        ->orStringLike($column, "{$value}")
                        ->orStringEndsLike($column, "{$value};")
                        ->orStringBeginsLike($column, ";{$value}")
                );
            }
            $builder->andWhere($constraints);
        }

        if (is_numeric($page)) {
            $limit = ($itemsPerPage ?? $field->getItemsPerPage()) ?? 10;
            $builder->pagination($page, $limit);
        }

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

        if ($where instanceof Where) {
            $builder->andWhere($where);
        }

        return $builder;
    }
}