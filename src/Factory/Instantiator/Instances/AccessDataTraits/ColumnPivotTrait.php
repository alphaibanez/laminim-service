<?php

namespace Lkt\Factory\Instantiator\Instances\AccessDataTraits;

use Exception;
use Lkt\Connectors\DatabaseConnections;
use Lkt\Debug\VarDumper;
use Lkt\Factory\Instantiator\Helpers\QueryBuilderHelper;
use Lkt\Factory\Instantiator\Instantiator;
use Lkt\Factory\Schemas\Exceptions\InvalidComponentException;
use Lkt\Factory\Schemas\Exceptions\InvalidSchemaAppClassException;
use Lkt\Factory\Schemas\Exceptions\SchemaNotDefinedException;
use Lkt\Factory\Schemas\Fields\AbstractField;
use Lkt\Factory\Schemas\Fields\PivotField;
use Lkt\Factory\Schemas\Fields\PivotRightIdField;
use Lkt\Factory\Schemas\Schema;
use Lkt\QueryBuilding\Query;
use Lkt\QueryBuilding\Where;

trait ColumnPivotTrait
{

    protected array $PIVOT_SORT = [];

    public function _getAvailablePivotQueryBuilder(string $column): Query
    {
        // Own fields
        $ownSchema = Schema::get(static::COMPONENT);
        $ownField = $ownSchema->getPivotField($column);
        $ownIdColumn = $ownSchema->getIdColumn();
        $ownIdColumn = reset($ownIdColumn);
        $ownIdField = $ownSchema->getField($ownIdColumn);

        // Pivot table fields (intermediate table)
        $pivotSchema = $ownField->getPivotSchema();
        $pivotField = $pivotSchema->getOneFieldPointingToComponent($ownField->getComponent());
        $pivotOwnField = $pivotSchema->getOneFieldPointingToComponent(static::COMPONENT);
        $pivotOrderField = $pivotSchema->getOnePositionField();

        // Referenced table
        $referencedSchema = Schema::get($ownField->getComponent());
        $referencedField = $referencedSchema->getField($referencedSchema->getIdColumn()[0]);
        $referencedIdColumn = $referencedSchema->getIdColumn();
        $referencedIdColumn = reset($referencedIdColumn);
        $referencedIdField = $referencedSchema->getField($referencedIdColumn);

        // Prepare query builder
        $referencedQueryBuilder = QueryBuilderHelper::getComponentQuery($referencedSchema->getComponent());
        $pivotQueryBuilder = QueryBuilderHelper::getComponentQuery($pivotSchema->getComponent());

        $pivotQueryBuilder
            ->setColumns([$pivotField->getColumn()])
            ->andIntegerEqual($pivotOwnField->getColumn(), $this->getIdColumnValue());

        $referencedQueryBuilder
            ->andFieldNotInSubQuery($referencedSchema->getTable() . '.' . $referencedField->getColumn(), $pivotQueryBuilder)
        ;

        return $referencedQueryBuilder;
    }

    public function _setPivotSort(string $column, array $data)
    {
        // Own fields
        $ownSchema = Schema::get(static::COMPONENT);
        $ownField = $ownSchema->getPivotField($column);

        // Referenced table
        $referencedSchema = Schema::get($ownField->getComponent());

        $idColumn = $referencedSchema->getIdColumn()[0];

        $items = array_map(function ($datum) use ($idColumn) {
            return $datum[$idColumn];
        }, $data);

        $this->PIVOT_SORT[$column] = $items;
        return $this;
    }


    /**
     * @param string $column
     * @return void
     * @throws InvalidComponentException
     * @throws SchemaNotDefinedException
     * @throws InvalidSchemaAppClassException
     * @throws Exception
     */
    private function _loadPivots(string $column)
    {
        $schema = Schema::get(static::COMPONENT);

        /** @var PivotField $field */
        $field = $schema->getField($column);
        $idColumn = $schema->getIdString();

        /** @var Schema $pivotedSchema */
        $pivotedSchema = $field->getPivotSchema();

        /** @var AbstractField $pivotedField */
        $pivotedField = $pivotedSchema->getOneFieldPointingToComponent(static::COMPONENT);

        $pivotedFieldColumn = trim($pivotedField->getColumn());

        $where = $field->getWhere();

        $order = $field->getOrder();
        $builder = Query::table($pivotedSchema->getTable());

        $connector = $schema->getDatabaseConnector();
        if ($connector === '') {
            $connector = DatabaseConnections::$defaultConnector;
        }
        $connection = DatabaseConnections::get($connector);
        $where[] = $connection->makeUpdateParams([$pivotedFieldColumn => $this->retrieveValue($idColumn)]);
        $builder->setColumns($connection->extractSchemaColumns($pivotedSchema));

        $builder->where(Where::raw(implode(' AND ', $where)));
        $builder->orderBy(implode(',', $order));

        $results = $builder->select();
        $pivots = Instantiator::makeResults($pivotedSchema->getComponent(), $results);

        $this->PIVOT[$column] = $pivots;
    }

    public function _getPivots(string $column)
    {
        if (!isset($this->PIVOT[$column])) {
            $this->_loadPivots($column);
        }
        return $this->PIVOT[$column];
    }


    /**
     * @param string $column
     * @return array
     * @throws InvalidComponentException
     * @throws InvalidSchemaAppClassException
     * @throws SchemaNotDefinedException
     */
    protected function _getPivotVal(string $column): array
    {
        return (array)$this->pivotData->getItems($column);
    }

    /**
     * @param string $column
     * @return bool
     * @throws InvalidComponentException
     * @throws InvalidSchemaAppClassException
     * @throws SchemaNotDefinedException
     */
    protected function _hasPivotVal(string $column = ''): bool
    {
        return $this->pivotData->has($column);
        return count($this->_getPivotVal($column)) > 0;
    }

//    protected function _getPivotTablePositionQueryBuilder(string $fieldName)
//    {
//        // Schema detection
//        $schema = Schema::get(static::COMPONENT);
//        $ownField = $schema->getField($fieldName);
//        /** @var Schema $pivotSchema */
//        $pivotSchema = $ownField->getPivotSchema();
//
//        // Position detection
//        $positionField = $pivotSchema->getOnePositionField();
//        $query = Query::table($pivotSchema->getTable());
//        $query->setColumns(["MAX({$positionField->getColumn()}) as lkt_position"]);
//
//        // Filter only to this element
//        $ownInstanceIdentifierFieldName = $schema->getIdColumn();
//
//        // Get related column at pivot table pointing to this schema
//        $pivotFieldPointingToMe = $pivotSchema->getOneFieldPointingToComponent(static::COMPONENT);
//
//        $query
//            ->andStringEqual($pivotFieldPointingToMe->getColumn(), $this->retrieveValue($ownInstanceIdentifierFieldName[0]));
//
//        $query->orderBy("{$positionField->getColumn()} ASC");
//
//        return $query;
//    }

//    protected function _getPivotTableInsertQueryBuilder(string $fieldName, mixed $relatedId, int $latestPosition)
//    {
//        // Schema detection
//        $schema = Schema::get(static::COMPONENT);
//        $ownField = $schema->getField($fieldName);
//        /** @var Schema $pivotSchema */
//        $pivotSchema = $ownField->getPivotSchema();
//
//        // Position detection
//        $positionField = $pivotSchema->getOnePositionField();
//        $query = Query::table($pivotSchema->getTable());
//
//        // Filter only to this element
//        $ownInstanceIdentifierFieldName = $schema->getIdColumn();
//
//        // Get related column at pivot table pointing to this schema
//        $pivotFieldPointingToMe = $pivotSchema->getOneFieldPointingToComponent(static::COMPONENT);
//
//        // Prepare data
//        $data = [
//            $positionField->getColumn() => $latestPosition + 1
//        ];
//        $data[$pivotFieldPointingToMe->getColumn()] = $this->retrieveValue($ownInstanceIdentifierFieldName[0]);
//
//        // Get related column at pivot table pointing the other schema
//        $fields = array_filter($pivotSchema->getRelationalFields(), function ($field) use ($pivotFieldPointingToMe) {
//            return $field->getColumn() !== $pivotFieldPointingToMe->getColumn();
//        });
//        /** @var PivotRightIdField $pivotFieldPointingToReferencedTable */
//        $pivotFieldPointingToReferencedTable = reset($fields);
////        $referencedSchema = Schema::get($pivotFieldPointingToReferencedTable->getComponent());
////
////        $referencedIdentifierFieldName = $schema->getIdColumn();
//        $data[$pivotFieldPointingToReferencedTable->getColumn()] = $relatedId;
//
//
//        $query->updateData($data);
//
//        return $query;
//    }

//    protected function _addPivotRelation(string $fieldName, mixed $relatedId): bool
//    {
//        $positionQuery = $this->_getPivotTablePositionQueryBuilder($fieldName);
//        $latestPosition = $positionQuery->select()[0]['lkt_position'];
//
//        $insertQuery = $this->_getPivotTableInsertQueryBuilder($fieldName, $relatedId, (int)$latestPosition);
//        return $insertQuery->insert();
//    }
}