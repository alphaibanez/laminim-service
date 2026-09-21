<?php

namespace Lkt\Factory\Instantiator\Helpers;

use Lkt\Connectors\DatabaseConnections;
use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Instantiator\ValueObjects\ComponentDatabaseIntegration;
use Lkt\Factory\Schemas\Fields\IntegerField;
use Lkt\Factory\Schemas\Fields\PivotField;
use Lkt\Factory\Schemas\Fields\RelatedField;
use Lkt\Factory\Schemas\Fields\RelatedKeysField;
use Lkt\Factory\Schemas\Fields\StringField;
use Lkt\Factory\Schemas\Schema;
use Lkt\QueryBuilding\Query;

class QueryBuilderHelper
{
    public static function getComponentQuery(Schema|string $component): Query|null
    {
        return (ComponentDatabaseIntegration::from($component))->query;
    }

    public static function prepareRelatedQuery(Item $item, Query $query, Schema $schema, RelatedField|RelatedKeysField $field, $forceRefresh = false, array $additionalData = []): Query
    {
        $relatedSchema = Schema::get($field->getComponent($schema, $item));

//        $where = (array)$field?->getWhere();

        $identifierValue = $item->getIdentifierValue();
        $idColumnValue = $identifierValue[array_keys($identifierValue)[0]];
        if (!$idColumnValue) return $query;

        // @todo test this as a replacement for complex primary key. If not working, remove this code block
        if ($relatedSchema->hasManyIdentifiers()) {
            $identifiers = $relatedSchema->getIdentifiers();
            $relatedField = $relatedSchema->getField($field->getColumn());
            foreach ($identifiers as $identifier) {
                $identifierName = $identifier->getName();

                if ($field instanceof RelatedField) {
                    if ($identifier instanceof IntegerField && $identifier->isForeignKey() && $additionalData[$identifierName] instanceof Item) {

                        if ($relatedField->getColumn() === $identifier->getColumn()) {
                            $query->andIntegerEqual($relatedField->getColumn(), $idColumnValue);
                        } else {
                            $query->andIntegerEqual($identifier->getColumn(), (int)$additionalData[$identifierName]?->getIdColumnValue());
                        }


                    } elseif ($identifier instanceof IntegerField) {

                        if ($relatedField->getColumn() === $identifier->getColumn()) {
                            $query->andIntegerEqual($relatedField->getColumn(), $idColumnValue);
                        } else {
                            $query->andIntegerEqual($identifier->getColumn(), $additionalData[$identifierName]);
                        }

                    } elseif ($identifier instanceof StringField) {

                        if ($relatedField->getColumn() === $identifier->getColumn()) {
                            $query->andStringEqual($relatedField->getColumn(), $idColumnValue);
                        } else {
                            $query->andStringEqual($identifier->getColumn(), $additionalData[$identifierName]);
                        }
                    }
                }

                elseif ($field instanceof RelatedKeysField) {
                    $column = $relatedField->getColumn();
                    $where = $relatedSchema->getWhereBuilder()
                        ->orStringLike($column, ";{$idColumnValue};")
                        ->orStringLike($column, "{$idColumnValue}")
                        ->orStringEndsLike($column, "{$idColumnValue};")
                        ->orStringBeginsLike($column, ";{$idColumnValue}");

                    $query->andWhere($where);
                }
            }

        } elseif (method_exists($field, 'hasMultipleReferences', ) && $field->hasMultipleReferences()) {
            foreach ($field->getMultipleReferences() as $reference) {
                $relatedField = $relatedSchema->getField($reference);

                if ($field instanceof RelatedField) {
                    if ($relatedField instanceof IntegerField) {
                        $query->andIntegerEqual($relatedField->getColumn(), $idColumnValue);

                    } elseif ($relatedField instanceof StringField) {
                        $query->andStringEqual($relatedField->getColumn(), $idColumnValue);
                    }

                } elseif ($field instanceof RelatedKeysField) {
                    $column = $relatedField->getColumn();
                    $where = $relatedSchema->getWhereBuilder()
                        ->orStringLike($column, ";{$idColumnValue};")
                        ->orStringLike($column, "{$idColumnValue}")
                        ->orStringEndsLike($column, "{$idColumnValue};")
                        ->orStringBeginsLike($column, ";{$idColumnValue}");

                    $query->andWhere($where);
                }
            }

        } elseif (!$item->isAnonymous()) {
            $relatedField = $relatedSchema->getField($field->getColumn());
            if ($field instanceof RelatedField) {
                if ($relatedField instanceof IntegerField) {
                    $query->andIntegerEqual($relatedField->getColumn(), $idColumnValue);

                } elseif ($relatedField instanceof StringField) {
                    $query->andStringEqual($relatedField->getColumn(), $idColumnValue);
                }

            } elseif ($field instanceof RelatedKeysField) {
                $column = $relatedField->getColumn();
                $where = $relatedSchema->getWhereBuilder()
                    ->orStringLike($column, ";{$idColumnValue};")
                    ->orStringLike($column, "{$idColumnValue}")
                    ->orStringEndsLike($column, "{$idColumnValue};")
                    ->orStringBeginsLike($column, ";{$idColumnValue}");

                $query->andWhere($where);
            }
        }

        $order = $field->getOrder();
        if (!is_array($order)) $order = [];

//        if (is_array($where) && count($where) > 0){
//            $query->andRaw(implode(' AND ', $where));
//        } elseif ($where instanceof Where) {
//            $query->andWhere($where);
//        }

        $query->orderBy(implode(',', $order));
        $query->setForceRefresh($forceRefresh);

        if ($field instanceof RelatedField && $field->isSingleMode()) $query->pagination(1, 1);

        return $query;
    }


    public static function preparePivotQuery(Item $item, PivotField $field, $forceRefresh = false): Query
    {
        $schema = $item->getSchema();
        $referencedComponent = $field->getComponent($schema, $item);
        $referencedSchema = Schema::get($referencedComponent);

        $pivotSchema = $field->getPivotSchema();
        $pivotField = $pivotSchema->getOneFieldPointingToComponent($referencedComponent);

        $sameTablePivot = $pivotSchema->isSameTablePivot();
        if ($sameTablePivot) {
            $pivotOwnField = $pivotSchema->getField($field->getColumn());
        } else {
            $pivotOwnField = $pivotSchema->getOneFieldPointingToComponent($schema->getComponent(), $pivotSchema);
        }

//        if (!$pivotOwnField) {
//            $instanceSettings = $schema->getInstanceSettings();
//            $extendedClass = $instanceSettings->getClassToBeExtended();
//            if ($extendedClass) {
//                try {
//                    $helperInstance = $extendedClass::getInstance();
//                    $ownComponent = $helperInstance->getSchema()->getComponent();
//                    $pivotOwnField = $pivotSchema->getOneFieldPointingToComponent($ownComponent);
//                } catch (\Exception $e) {
//
//                }
//            }
//        }

        $pivotOrderField = $pivotSchema->getOnePositionField();


        // Referenced table
        $referencedField = $referencedSchema->getField($referencedSchema->getIdColumn()[0]);

        // Prepare query builder
        $query = QueryBuilderHelper::getComponentQuery($field->getComponent());
        $pivotQueryBuilder = QueryBuilderHelper::getComponentQuery($pivotSchema);

        $pivotQueryBuilder
            ->andIntegerEqual($pivotOwnField->getColumn(), $item->getIdColumnValue());

        $query
            ->leftJoin($pivotQueryBuilder, $pivotField->getColumn(), $referencedField->getColumn())
            ->orderBy($pivotOrderField->getColumn() . ' ASC')
        ;


        return $query;
    }


    public static function preparePivotsDataQuery(Item $item, PivotField $field, $forceRefresh = false): Query
    {
        $schema = $item->getSchema();
        $referencedComponent = $field->getComponent($schema, $item);

        $pivotSchema = $field->getPivotSchema();
        $pivotField = $pivotSchema->getOneFieldPointingToComponent($referencedComponent);

        $sameTablePivot = $pivotSchema->isSameTablePivot();
        if ($sameTablePivot) {
            $pivotOwnField = $pivotSchema->getField($field->getColumn());
        } else {
            $pivotOwnField = $pivotSchema->getOneFieldPointingToComponent($schema->getComponent());
        }

        $pivotOrderField = $pivotSchema->getOnePositionField();

        $where = $field->getWhere();


        // Prepare query builder
        $query = QueryBuilderHelper::getComponentQuery($pivotSchema->getComponent());

        $query
            ->andIntegerEqual($pivotOwnField->getColumn(), $item->getIdColumnValue());

        $query
            ->orderBy($pivotOrderField->getColumn() . ' ASC')
        ;

        $connector = $schema->getDatabaseConnector();
        if ($connector === '') {
            $connector = DatabaseConnections::$defaultConnector;
        }
        $connection = DatabaseConnections::get($connector);

        $query->setColumns($connection->extractSchemaColumns($pivotSchema));

        return $query;
    }
}