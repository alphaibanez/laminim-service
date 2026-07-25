<?php

namespace Lkt\Factory\Instantiator\Instances\AccessDataTraits;

use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Instance\Traits\ItemWithRelatedItemDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithRelatedItemsDataTrait;
use Lkt\Factory\Schemas\Exceptions\InvalidComponentException;
use Lkt\Factory\Schemas\Exceptions\InvalidSchemaAppClassException;
use Lkt\Factory\Schemas\Exceptions\SchemaNotDefinedException;
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
    }

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
        $this->relatedItemsData->setItems($column, $data);
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