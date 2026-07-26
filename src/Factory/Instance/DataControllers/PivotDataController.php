<?php

namespace Lkt\Factory\Instance\DataControllers;

use Lkt\Connectors\DatabaseConnections;
use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Instantiator\Helpers\QueryBuilderHelper;
use Lkt\Factory\Instantiator\Instantiator;
use Lkt\Factory\Schemas\Fields\AbstractField;
use Lkt\Factory\Schemas\Fields\PivotLeftIdField;
use Lkt\Factory\Schemas\Fields\PivotPositionField;
use Lkt\Factory\Schemas\Fields\PivotRightIdField;
use Lkt\Factory\Schemas\Schema;
use Lkt\QueryBuilding\Query;
use Lkt\QueryBuilding\Where;
use function Lkt\Tools\Pagination\getTotalPages;

final class PivotDataController
{
    private array $data = [];
    private array $payload = [];
    private array $needsUpdate = [];
    private array $itemsCount = [];
    private array $itemsAmountOfPages = [];

    private array $pendingLinks = [];
    private array $pivots = [];

    private Schema $schema;
    private Item $item;

    public function __construct(Schema &$schema, Item &$ins)
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

        return array_map(function (Item $item){
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
        if (count($ids[0]) === 0) {
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

        $field = $this->schema->getPivotField($key);
        if (!$field) return null;

        if (isset($this->payload[$cacheKey])) return $this->payload[$cacheKey];

        if (isset($this->data[$cacheKey])) return $this->data[$cacheKey];

//        /** @var Schema $pivotSchema */
//        $pivotSchema = $field->getPivotSchema();
//
//        $pivotIdentifiers = $pivotSchema->getIdentifiers();
//        $pivotForeignColumn = null;
//        foreach ($pivotIdentifiers as $identifier) {
//            if ($identifier instanceof PivotLeftIdField || $identifier instanceof PivotRightIdField) {
//                if ($identifier->getComponent() === $field->getComponent($this->schema, $this->item)) {
//                    $pivotForeignColumn = $identifier;
//                    break;
//                }
//            }
//        }
//
//        $toSchema = Schema::get($pivotForeignColumn->getComponent());
        $toSchema = Schema::get($field->getTargetComponent($this->schema, $this->item));

        $query = QueryBuilderHelper::preparePivotQuery($this->item, $field, $forceRefresh);
        $results = Instantiator::makeResults($toSchema->getComponent(), $query->select());

        $this->data[$cacheKey] = $results;
        return $this->data[$cacheKey];
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

        $builder = QueryBuilderHelper::prepareRelatedQuery(
            $this->item,
            QueryBuilderHelper::getComponentQuery($field->getComponent()),
            $this->schema,
            $field,
        );

        if ($where instanceof Where) {
            $builder->andWhere($where);
        }

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
            'pendingLinks' => $this->pendingLinks,
        ];
    }

    public function setItems(string $key, array $items, string $accessPolicy = 'lkt-related')
    {
        $field = $this->schema->getPivotField($key);
        if (!$field) return null;

        // Referenced table
        $referencedSchema = Schema::get($field->getComponent($this->schema, $this->item));

        $idColumn = $referencedSchema->getIdColumn()[0];

        $items = array_map(function ($datum) use ($idColumn) {
            return $datum[$idColumn];
        }, $items);

        $this->needsUpdate[$key] = $items;
        return $this;

        // @remember Changes won't be persistent until $this->item was saved!
//        $this->needsUpdate[$key] = $itemsWithFedData;
//        $this->payload[$key] = $items;
    }

    public function save(): self
    {
        foreach ($this->needsUpdate as $key => $ids) {

            $field = $this->schema->getPivotField($key);

            // Pivot table fields (intermediate table)
            $pivotSchema = $field->getPivotSchema();

            $pointingField = $pivotSchema->getOneFieldPointingToComponent($this->schema->getComponent());

            if ($pointingField instanceof PivotLeftIdField) {
                $referencedField = $pivotSchema->getPivotRightIdField();
            } else {
                $referencedField = $pivotSchema->getPivotLeftIdField();
            }

            /** @var PivotPositionField $positionField */
            $positionField = $pivotSchema->getOnePositionField();

            $positionKey = $positionField->getName();
            $referencedKey = $referencedField->getName();
            $pointingKey = $pointingField->getName();

            $idColumnValue = $this->item->getIdColumnValue();

            $results = $this->getPivots($field->getName());

            $checkedIds = [];

            $itemsToCreate = [];
            $itemsToUpdate = [];
            $itemsToDelete = [];

            // Update existing pivots
            /** @var Item $result */
            foreach ($results as $result) {
                $id = $result->retrieveValue($referencedKey);
                $position = $result->retrieveValue($positionKey);

                $updatedPosition = array_search($id, $ids);
                $checkedIds[] = $id;

                if ($updatedPosition !== $position) {
                    $result->assignValue($positionKey, $updatedPosition);
                    $itemsToUpdate[] = $result;
                }

                // Unlink pivot relation
                if (!in_array($id, $ids, true)) {
                    $itemsToDelete[] = $result;
                }
            }

            // Link new pivot relations
            foreach ($ids as $i => $id) {
                if (!in_array($id, $checkedIds, true)) {
                    $ins = $pivotSchema->getItemInstance();
                    $ins->assignValue($pointingKey, $idColumnValue)
                        ->assignValue($referencedKey, $id)
                        ->assignValue($positionKey, $i);
                    $itemsToCreate[] = $ins;
                }
            }

//            $anonymous = $pivotSchema->getItemInstance();

            if (count($itemsToCreate) > 0) {
                $createBatchActions = $pivotSchema->getBatchActions($itemsToCreate);
                $createBatchActions->create();
            }

            if (count($itemsToUpdate) > 0) {
                $createBatchActions = $pivotSchema->getBatchActions($itemsToUpdate);
                $createBatchActions->update();
            }

            if (count($itemsToDelete) > 0) {
                $createBatchActions = $pivotSchema->getBatchActions($itemsToDelete);
                $createBatchActions->drop();
            }
        }

        return $this;
    }

    public function hasToSave(): bool
    {
        return count($this->needsUpdate) > 0;
    }

    public function prepareToLink(string $key, int|array $parentIdentifierValue): self
    {
        $this->pendingLinks[$key] = $parentIdentifierValue;
        return $this;
    }

    public function hasToLink(): bool
    {
        return count($this->pendingLinks) > 0;
    }

    public function getPendingLinks(): array
    {
        return $this->pendingLinks;
    }

    public function linkPendingPivots(): self
    {
        foreach ($this->pendingLinks as $key => $relatedId) {
            $field = $this->schema->getPivotField($key);
            if (!$field) continue;

            $relatedId = $this->pendingLinks[$key];

            $idColumnValue = $this->item->getIdColumnValue();

            $pivotSchema = $field->getPivotSchema();

            // Position detection
            $positionField = $pivotSchema->getOnePositionField();
            $positionQuery = Query::table($pivotSchema->getTable());
            $positionQuery->setColumns(["MAX({$positionField->getColumn()}) as lkt_position"]);

            // Get related column at pivot table pointing to this schema
            $pivotFieldPointingToMe = $pivotSchema->getOneFieldPointingToComponent($this->schema->getComponent());

            $positionQuery
                ->andStringEqual($pivotFieldPointingToMe->getColumn(), $idColumnValue);

            $positionQuery->orderBy("{$positionField->getColumn()} ASC");

            $latestPosition = $positionQuery->select()[0]['lkt_position'];


            // Position detection
            $insertQuery = Query::table($pivotSchema->getTable());

            // Prepare data
            $data = [
                $positionField->getColumn() => $latestPosition + 1
            ];
            $data[$pivotFieldPointingToMe->getColumn()] = $idColumnValue;

            // Get related column at pivot table pointing the other schema
            $fields = array_filter($pivotSchema->getRelationalFields(), function ($field) use ($pivotFieldPointingToMe) {
                return $field->getColumn() !== $pivotFieldPointingToMe->getColumn();
            });
            /** @var PivotRightIdField $pivotFieldPointingToReferencedTable */
            $pivotFieldPointingToReferencedTable = reset($fields);
            $data[$pivotFieldPointingToReferencedTable->getColumn()] = $relatedId;


            $insertQuery->updateData($data);

            $insertQuery->insert();
        }

        return $this;
    }

    public function getPivots(string $key): array|null
    {
        $field = $this->schema->getPivotField($key);
        $idColumn = $this->schema->getIdString();

        /** @var Schema $pivotedSchema */
        $pivotedSchema = $field->getPivotSchema();

        /** @var AbstractField $pivotedField */
        $pivotedField = $pivotedSchema->getOneFieldPointingToComponent($this->schema->getComponent());

        $pivotedFieldColumn = trim($pivotedField->getColumn());

        $where = $field->getWhere();

        $order = $field->getOrder();
        $builder = Query::table($pivotedSchema->getTable());

        $connector = $this->schema->getDatabaseConnector();
        if ($connector === '') {
            $connector = DatabaseConnections::$defaultConnector;
        }
        $connection = DatabaseConnections::get($connector);
        $where[] = $connection->makeUpdateParams([$pivotedFieldColumn => $this->item->retrieveValue($idColumn)]);
        $builder->setColumns($connection->extractSchemaColumns($pivotedSchema));

        $builder->where(Where::raw(implode(' AND ', $where)));
        $builder->orderBy(implode(',', $order));

        $results = $builder->select();
        $pivots = Instantiator::makeResults($pivotedSchema, $results);

        $this->pivots[$key] = $pivots;
        return $this->pivots[$key];
    }

    public function link(string $key, $id): static
    {
        $field = $this->schema->getPivotField($key);
        $component = $field->getComponent($this->schema, $this->item);

        $pivotSchema = Schema::get($component);

        $pointingField = $pivotSchema->getOneFieldPointingToComponent($this->schema->getComponent());

        if ($pointingField instanceof PivotLeftIdField) {
            $referencedField = $pivotSchema->getPivotRightIdField();
        } else {
            $referencedField = $pivotSchema->getPivotLeftIdField();
        }

        /** @var PivotPositionField $positionField */
        $positionField = $pivotSchema->getOnePositionField();

        $pivotQueryBuilder = QueryBuilderHelper::getComponentQuery($component);

        $pivotQueryBuilder->setColumns(["MAX({$positionField->getColumn()}) AS {$positionField->getName()}"]);

        $results = $pivotQueryBuilder->select();
        $nextPosition = $results[0]['position'] === null ? 0 : (int)$results[0]['position'] + 1;


        $instance = $pivotSchema->getItemInstance();

        $pointingSetter = $pointingField->getSetterForPrimitiveValue();
        $instance->{$pointingSetter}($this->item->getIdColumnValue());

        $referencedSetter = $referencedField->getSetterForPrimitiveValue();
        $instance->{$referencedSetter}($id);

        $positionSetter = $positionField->getSetterForPrimitiveValue();
        $instance->{$positionSetter}($nextPosition);

        $instance->save();
        return $this;
    }

    public function unlink(string $key, $id): static
    {
        $field = $this->schema->getPivotField($key);
        $component = $field->getComponent($this->schema, $this->item);

        $pivotSchema = Schema::get($component);

        $pointingField = $pivotSchema->getOneFieldPointingToComponent($this->schema->getComponent());

        if ($pointingField instanceof PivotLeftIdField) {
            $referencedField = $pivotSchema->getPivotRightIdField();
        } else {
            $referencedField = $pivotSchema->getPivotLeftIdField();
        }

        $pivotQueryBuilder = QueryBuilderHelper::getComponentQuery($component);

        $pointingGetter = $pointingField->getGetterForPrimitiveValue();
        $pivotQueryBuilder->andIntegerEqual($pointingField->getColumn(), $this->{$pointingGetter}());

        $referencedGetter = $referencedField->getGetterForPrimitiveValue();
        $pivotQueryBuilder->andIntegerEqual($referencedField->getColumn(), $this->{$referencedGetter}());

        $anonymous = $pivotSchema->getItemInstance();
        $instance = $anonymous::getOne($pivotQueryBuilder);
        $instance->delete();
        return $this;
    }
}