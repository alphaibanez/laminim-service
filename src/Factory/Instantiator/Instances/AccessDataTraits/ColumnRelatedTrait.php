<?php

namespace Lkt\Factory\Instantiator\Instances\AccessDataTraits;

use Lkt\Debug\VarDumper;
use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Instance\Traits\ItemWithRelatedItemDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithRelatedItemsDataTrait;
use Lkt\Factory\Instantiator\Helpers\UpdatedRelatedDataProcessor;
use Lkt\Factory\Schemas\Exceptions\InvalidComponentException;
use Lkt\Factory\Schemas\Exceptions\InvalidSchemaAppClassException;
use Lkt\Factory\Schemas\Exceptions\SchemaNotDefinedException;
use Lkt\Factory\Schemas\Schema;
use Lkt\QueryBuilding\Where;

trait ColumnRelatedTrait
{
    use ItemWithRelatedItemDataTrait,
        ItemWithRelatedItemsDataTrait;

    /**
     * @param string $type
     * @param $column
     * @param $forceRefresh
     * @return array
     * @throws InvalidComponentException
     * @throws SchemaNotDefinedException
     */
    protected function _getRelatedVal(string $type = '', $column = '', $forceRefresh = false, array $additionalData = []): array
    {
        return $this->relatedItemsData->getItems($column, null, null, null, $additionalData, $forceRefresh) ?? [];
    }

    /**
     * @param string $type
     * @param $column
     * @param $forceRefresh
     * @return null|\Lkt\Factory\Instantiator\Instances\AbstractInstance|Item
     * @throws InvalidComponentException
     * @throws InvalidSchemaAppClassException
     * @throws SchemaNotDefinedException
     */
    protected function _getRelatedValSingle(string $type = '', $column = '', $forceRefresh = false, array $additionalData = [])
    {
        return $this->relatedItemData->getItem($column, $additionalData);
    }

    /**
     * @throws SchemaNotDefinedException
     */
    protected function _getRelatedQueryBuilder($type = '', $column = '', $forceRefresh = false, array $additionalData = [])
    {
        return $this->relatedItemsData->getQuery($column, null, null, null, $additionalData, $forceRefresh);
    }

    /**
     * @throws SchemaNotDefinedException
     */
    protected function _getRelatedQueryCaller($type = '', $column = '', $forceRefresh = false, array $additionalData = [])
    {
        return $this->relatedItemsData->getQuery($column, null, null, null, $additionalData, $forceRefresh);
    }

    /**
     * @throws SchemaNotDefinedException
     */
    protected function _getRelatedCustomQueryBuilder($type = '', $column = '', $forceRefresh = false, array $additionalData = [])
    {
        return $this->relatedItemsData->getQuery($column, null, null, null, $additionalData, $forceRefresh);
//
//        $schema = Schema::get(static::COMPONENT);
//        $field = $schema->getRelatedField($column);
//
//        /**
//         * @var Query $builder
//         * @var DatabaseConnector $connection
//         */
//        list($builder) = Instantiator::getCustomQueryCaller($field->getComponent());
//
//        return $this->_prepareQuery($builder, $schema, $field, $forceRefresh);
    }

//    protected function _prepareQuery(Query $query, Schema $schema, RelatedField $field, $forceRefresh = false, array $additionalData = [])
//    {
//        $idColumn = $schema->getIdString();
//        $relatedSchema = Schema::get($field->getComponent());
//
//        $where = (array)$field?->getWhere();
//
//        if ($relatedSchema->hasComplexPrimaryKey()) {
//            $identifiers = $relatedSchema->getIdentifiers();
//            $relatedField = $relatedSchema->getField($field->getColumn());
//            foreach ($identifiers as $identifier) {
//                $identifierName = $identifier->getName();
//
//                if ($identifier instanceof ForeignKeyField && $additionalData[$identifierName] instanceof AbstractInstance) {
//
//                    if ($relatedField->getColumn() === $identifier->getColumn()) {
//                        $query->andIntegerEqual($relatedField->getColumn(), $this->DATA[$idColumn]);
//                    } else {
//                        $query->andIntegerEqual($identifier->getColumn(), (int)$additionalData[$identifierName]?->getIdColumnValue());
//                    }
//
//
//                }elseif ($identifier instanceof IntegerField) {
//
//                    if ($relatedField->getColumn() === $identifier->getColumn()) {
//                        $query->andIntegerEqual($relatedField->getColumn(), $this->DATA[$idColumn]);
//                    } else {
//                        $query->andIntegerEqual($identifier->getColumn(), $additionalData[$identifierName]);
//                    }
//
//                } elseif ($identifier instanceof StringField) {
//
//                    if ($relatedField->getColumn() === $identifier->getColumn()) {
//                        $query->andStringEqual($relatedField->getColumn(), $this->DATA[$idColumn]);
//                    } else {
//                        $query->andStringEqual($identifier->getColumn(), $additionalData[$identifierName]);
//                    }
//                }
//            }
//
//        } else {
//            if ($field->hasMultipleReferences()) {
//                foreach ($field->getMultipleReferences() as $reference) {
//                    $relatedField = $relatedSchema->getField($reference);
//                    if ($relatedField instanceof IntegerField) {
//                        $query->andIntegerEqual($relatedField->getColumn(), $this->DATA[$idColumn]);
//
//                    } elseif ($relatedField instanceof StringField) {
//                        $query->andStringEqual($relatedField->getColumn(), $this->DATA[$idColumn]);
//                    }
//                }
//
//            } else {
//                if ($this->DATA[$idColumn]) {
//                    $relatedField = $relatedSchema->getField($field->getColumn());
//                    if ($relatedField instanceof IntegerField) {
//                        $query->andIntegerEqual($relatedField->getColumn(), $this->DATA[$idColumn]);
//
//                    } elseif ($relatedField instanceof StringField) {
//                        $query->andStringEqual($relatedField->getColumn(), $this->DATA[$idColumn]);
//                    }
//                }
//            }
//        }
//
//        $order = $field->getOrder();
//        if (!is_array($order)) $order = [];
//
//        if (count($where) > 0){
//            $query->andRaw(implode(' AND ', $where));
//        }
//
//        $query->orderBy(implode(',', $order));
//        $query->setForceRefresh($forceRefresh);
//
//        if ($field->isSingleMode()) $query->pagination(1, 1);
//
//        return $query;
//    }

    /**
     * @throws InvalidComponentException
     * @throws SchemaNotDefinedException
     */
    protected function _getRelatedCustomQueryCaller($type = '', $column = '', $forceRefresh = false)
    {
        return $this->_getRelatedCustomQueryBuilder($type, $column, $forceRefresh);
    }

    /**
     * @param string $type
     * @param string $column
     * @return bool
     */
    protected function _hasRelatedVal($type = '', $column = ''): bool
    {
        return count($this->_getRelatedVal($type)) > 0;
    }

    /**
     * @throws InvalidComponentException
     * @throws InvalidSchemaAppClassException
     * @throws SchemaNotDefinedException
     */
    protected function _setRelatedValWithData($type = '', $column = '', $data = [])
    {
        VarDumper::die('toca hacer esto');
        $schema = Schema::get(static::COMPONENT);
        $accessPolicy = 'lkt-related';
        $field = $schema->getField($column);
        if ($this->accessPolicy) {
            $auxAccessPolicy = $field->getAssociatedAccessPolicy($this->accessPolicy->name);
            if ($auxAccessPolicy) $accessPolicy = $auxAccessPolicy;
        }

        $dataProcessor = new UpdatedRelatedDataProcessor(
            $schema,
            $column,
            $data,
            $this,
            $accessPolicy
        );
        $dataProcessor->processRelatedField();

        $this->PENDING_UPDATE_RELATED_DATA[$column] = $dataProcessor->pendingUpdateData;
        $this->UPDATED_RELATED_DATA[$column] = $dataProcessor->updatedData;
        return $this;
    }

    protected function _getRelatedPage(string $type, string $fieldName, int $page = 1, Where $where = null)
    {
        return $this->relatedItemsData->getItems($fieldName, $where, $page);
    }

    protected function _getRelatedCount(string $type, string $fieldName, string $countableField = '', Where $where = null)
    {
        return $this->relatedItemsData->getItemsCount($fieldName, $where, $countableField);
    }

    protected function _getRelatedAmountOfPages(string $type, string $fieldName, string $countableField = '', Where $where = null)
    {
        return $this->relatedItemsData->getItemsAmountOfPages($fieldName, $where, $countableField);
    }
}