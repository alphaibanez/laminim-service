<?php

namespace Lkt\Factory\Instantiator\Helpers;

use Lkt\Debug\VarDumper;
use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Instantiator\Instances\AbstractInstance;
use Lkt\Factory\Instantiator\ValueObjects\ComponentDatabaseIntegration;
use Lkt\Factory\Schemas\Fields\ForeignKeyField;
use Lkt\Factory\Schemas\Fields\IntegerField;
use Lkt\Factory\Schemas\Fields\RelatedField;
use Lkt\Factory\Schemas\Fields\StringField;
use Lkt\Factory\Schemas\Schema;
use Lkt\QueryBuilding\Query;

class QueryBuilderHelper
{
    public static function getComponentQuery(string $component): Query
    {
        return (ComponentDatabaseIntegration::from($component))->query;
    }

    public static function prepareRelatedQuery(Item $item, Query $query, Schema $schema, RelatedField $field, $forceRefresh = false, array $additionalData = []): Query
    {
        $idColumn = $schema->getIdString();
        $relatedSchema = Schema::get($field->getComponent());

        $where = (array)$field?->getWhere();

        $identifierValue = $item->getIdentifierValue();

        if ($relatedSchema->hasComplexPrimaryKey()) {
            $identifiers = $relatedSchema->getIdentifiers();
            $relatedField = $relatedSchema->getField($field->getColumn());
            foreach ($identifiers as $identifier) {
                $identifierName = $identifier->getName();

                if ($identifier instanceof ForeignKeyField && $additionalData[$identifierName] instanceof AbstractInstance) {

                    if ($relatedField->getColumn() === $identifier->getColumn()) {
                        $query->andIntegerEqual($relatedField->getColumn(), $identifierValue[$relatedField->getName()]);
                    } else {
                        $query->andIntegerEqual($identifier->getColumn(), (int)$additionalData[$identifierName]?->getIdColumnValue());
                    }


                }elseif ($identifier instanceof IntegerField) {

                    if ($relatedField->getColumn() === $identifier->getColumn()) {
                        $query->andIntegerEqual($relatedField->getColumn(), $identifierValue[$relatedField->getName()]);
                    } else {
                        $query->andIntegerEqual($identifier->getColumn(), $additionalData[$identifierName]);
                    }

                } elseif ($identifier instanceof StringField) {

                    if ($relatedField->getColumn() === $identifier->getColumn()) {
                        $query->andStringEqual($relatedField->getColumn(), $identifierValue[$relatedField->getName()]);
                    } else {
                        $query->andStringEqual($identifier->getColumn(), $additionalData[$identifierName]);
                    }
                }
            }

        } else {
            if ($field->hasMultipleReferences()) {
                foreach ($field->getMultipleReferences() as $reference) {
                    $relatedField = $relatedSchema->getField($reference);
                    if ($relatedField instanceof IntegerField) {
                        $query->andIntegerEqual($relatedField->getColumn(), $identifierValue[$relatedField->getName()]);

                    } elseif ($relatedField instanceof StringField) {
                        $query->andStringEqual($relatedField->getColumn(), $identifierValue[$relatedField->getName()]);
                    }
                }

            } else {
                if (!$item->isAnonymous()) {
                    $identifiers = $relatedSchema->getIdentifiers();
                    $relatedField = $relatedSchema->getField($field->getColumn());
                    if ($relatedField instanceof IntegerField) {
                        $query->andIntegerEqual($relatedField->getColumn(), $identifierValue[$identifiers[0]->getName()]);

                    } elseif ($relatedField instanceof StringField) {
                        $query->andStringEqual($relatedField->getColumn(), $identifierValue[$identifiers[0]->getName()]);
                    }
                }
            }
        }

        $order = $field->getOrder();
        if (!is_array($order)) $order = [];

        if (count($where) > 0){
            $query->andRaw(implode(' AND ', $where));
        }

        $query->orderBy(implode(',', $order));
        $query->setForceRefresh($forceRefresh);

        if ($field->isSingleMode()) $query->pagination(1, 1);

        return $query;
    }
}