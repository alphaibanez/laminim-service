<?php

namespace Lkt\Factory\Instance\DataControllers;

use Lkt\Connectors\DatabaseConnections;
use Lkt\Factory\Instance\Enums\RetrieveDataMode;
use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Instantiator\Helpers\QueryBuilderHelper;
use Lkt\Factory\Instantiator\Instances\AbstractInstance;
use Lkt\Factory\Instantiator\Instantiator;
use Lkt\Factory\Instantiator\Relations\RelatedKeysMergeHelper;
use Lkt\Factory\Schemas\Fields\RelatedKeysMergeField;
use Lkt\Factory\Schemas\Schema;
use Lkt\QueryBuilding\Where;
use function Lkt\Tools\Arrays\compareArrays;
use function Lkt\Tools\Pagination\getTotalPages;

final class RelatedItemsDataController
{
    private array $data = [];
    private array $rawData = [];
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

    public function has(
        string     $key,
        Where|null $where = null,
        int|null   $page = null,
        int|null   $itemsPerPage = null,
        array      $additionalData = [],
        bool       $forceRefresh = false
    ): bool
    {
        $v = $this->getItems($key, $where, $page, $itemsPerPage, $additionalData, $forceRefresh);
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
        bool       $forceRefresh = false,
        bool       $returnRawResults = false
    ): array|null
    {
        if ($this->item->isAnonymous()) return null;

        $cacheKey = [$key];
        if ($returnRawResults) $cacheKey[] = 'raw';
        if ($page !== null) $cacheKey[] = $page;
        $cacheKey = implode('-', $cacheKey);

        if (!$forceRefresh) {
            if (array_key_exists($cacheKey, $this->payload)) return $this->payload[$cacheKey];
            if (array_key_exists($cacheKey, $this->data)) return $this->data[$cacheKey];
        }

        $field = $this->schema->getKindOfRelatedField($key);
        if (!$field) return null;

        $builder = $this->getQuery($key, $where, $page, $itemsPerPage, $additionalData);

        if ($field instanceof RelatedKeysMergeField) {
            $data = RelatedKeysMergeHelper::getRawResultsFromQueryUnion($this->schema->getComponent(), $key, $builder);
            if ($returnRawResults) {
                $results = $data;
            } else {
                $results = RelatedKeysMergeHelper::convertRawResults($data);
            }

        } else {
            $data = $builder->select();
            $relatedSchema = Schema::get($field->getComponent());
            $results = Instantiator::makeResults($relatedSchema->getComponent(), $data);
        }

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

        if ($field instanceof RelatedKeysMergeField) {
            $builder = $this->getQuery($key, $where);
            $builder->countMode();
            $query = $builder->toString();

            $connector = $this->schema->getDatabaseConnector();
            if ($connector === '') $connector = DatabaseConnections::$defaultConnector;
            $connection = DatabaseConnections::get($connector);
            $response = $connection->query($query);
            return (int)$response[0]['Total'];

        } else {
            $builder = $this->getQuery($key, $where);
            $c = $builder->count($countableField);
        }

        $this->itemsCount[$key] = $c;
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
            'needsUpdate' => $this->needsUpdate,
            'appendItemsInParentForeignKeysFieldStack' => $this->appendItemsInParentForeignKeysFieldStack,
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

        $relatedFieldPointingMe = $relatedSchema->getField($field->getColumn());
        $relatedFieldPointingMeKey = $relatedFieldPointingMe->getName();

        $itemsWithFedData = [];

        foreach ($items as $item) {
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
         * @var Item[] $items
         */
        foreach ($this->needsUpdate as $key => $items) {

            $field = $this->schema->getKindOfRelatedField($key);

            $relatedComponent = $field->getComponent($this->schema, $this->item);
            $relatedSchema = Schema::get($relatedComponent);
            /** @var AbstractInstance $relatedClass */
            $relatedClass = $relatedSchema->getInstanceSettings()->getAppClass();

            $currentIds = $this->getItemsIds($key, null, null, null, [], true);
            $updatedIds = [];


            $itemsToCreate = [];
            $itemsToUpdate = [];
            $itemsToDelete = [];

            foreach ($items as $item) {
                if ($item->isAnonymous()) {
                    $itemsToCreate[] = $item;

                } else {
                    $itemsToUpdate[] = $item;
                    $updatedIds[] = $item->getIdColumnValue();
                }
            }

            // Update instances
            if (count($itemsToUpdate) > 0) {
                $batchActions = $relatedSchema->getBatchActions($itemsToUpdate);
                $batchActions->update();
            }

            // Create instances
            if (count($itemsToCreate) > 0) {
                $batchActions = $relatedSchema->getBatchActions($itemsToCreate);
                $batchActions->create();
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

        $this->needsUpdate = [];
        $this->payload = [];

        return $this;
    }

    public function hasToAppendItemsInParentForeignKeysField(): bool
    {
        return count($this->appendItemsInParentForeignKeysFieldStack) > 0;
    }

    public function prepareToAppendItemsInParentForeignKeysField(string $key, int|array $parentIdentifierValue): self
    {
        $field = $this->schema->getRelatedKeysField($key);

        $this->appendItemsInParentForeignKeysFieldStack[$field->getName()] = $parentIdentifierValue;
        return $this;
    }

    public function appendItemsInParentForeignKeysField(): self
    {
        $idColumnValue = $this->item->getIdColumnValue();

        foreach ($this->appendItemsInParentForeignKeysFieldStack as $key => $id) {

            $field = $this->schema->getRelatedKeysField($key);
            if (!$field) continue;

            $relatedSchema = Schema::get($field->getComponent($this->schema, $this->item));
            $relatedSchemaField = $relatedSchema->getField($field->getColumn());
            $relatedDatum = $relatedSchemaField->getName();

            $parentIds = is_array($id) ? $id : [$id];
            $instancesToUpdate = [];
            foreach ($parentIds as $parentId) {
                $parentInstance = $relatedSchema->getItemInstance($parentId);
                if (!$parentInstance->isAnonymous()) {
                    $currentIds = $parentInstance->retrieveValue($relatedDatum, [], RetrieveDataMode::Ids);

                    if (!in_array($idColumnValue, $currentIds)) {
                        $parentInstance->assignValue($relatedDatum, [
                            ...$currentIds, $idColumnValue
                        ], RetrieveDataMode::Ids);

                        $instancesToUpdate[] = $parentInstance;
                    }
                }
            }

            if (count($instancesToUpdate) > 0) {
                $batchActions = $relatedSchema->getBatchActions($instancesToUpdate);
                $batchActions->update();
            }
        }


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

        if ($field instanceof RelatedKeysMergeField) {
            $builder = RelatedKeysMergeHelper::getQueryUnion(
                $this->schema->getComponent(),
                $key,
                $this->item->getIdColumnValue()
            );

        } else {

            $targetComponent = $field->getComponent($this->schema, $this->item);

            $builder = QueryBuilderHelper::prepareRelatedQuery(
                $this->item,
                QueryBuilderHelper::getComponentQuery($targetComponent),
                $this->schema,
                $field,
                $forceRefresh,
                $additionalData,
            );
        }

//        VarDumper::dump($this->schema->getComponent(), $field->getComponent($this->schema,$this->item), $field, $builder);

//        if ($field instanceof RelatedKeysField) {
//            $constraints = $this->item::getWhereBuilder();
//            foreach ($this->item->getIdentifierValue() as $column => $value) {
//                $constraints->andWhere(
//                    $this->item::getWhereBuilder()
//                        ->orStringLike($column, ";{$value};")
//                        ->orStringLike($column, "{$value}")
//                        ->orStringEndsLike($column, "{$value};")
//                        ->orStringBeginsLike($column, ";{$value}")
//                );
//            }
//            $builder->andWhere($constraints);
//        }

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

    public function hasToSave(): bool
    {
        return count($this->needsUpdate) > 0;
    }
}