<?php

namespace Lkt\Factory\Instantiator\Instances\AccessDataTraits;

use Lkt\Factory\Instance\Traits\ItemWithForeignKeysDataTrait;
use Lkt\Factory\Schemas\Exceptions\InvalidComponentException;
use Lkt\Factory\Schemas\Exceptions\InvalidSchemaAppClassException;
use Lkt\Factory\Schemas\Exceptions\SchemaNotDefinedException;

trait ColumnForeignListTrait
{
    use ItemWithForeignKeysDataTrait;

    /**
     * @deprecated
     * @param string $fieldName
     * @return array
     * @throws InvalidComponentException
     * @throws SchemaNotDefinedException
     */
    protected function _getForeignListIds(string $fieldName): array
    {
        return $this->foreignKeysData->getIds($fieldName) ?? [];
    }

    /**
     * @deprecated
     * @param string $fieldName
     * @return array
     * @throws InvalidComponentException
     * @throws SchemaNotDefinedException
     * @throws InvalidSchemaAppClassException
     */
    protected function _getForeignListData(string $fieldName): array
    {
        return $this->foreignKeysData->getItems($fieldName) ?? [];
    }

    /**
     * @deprecated
     * @param string $fieldName
     * @return string
     */
    protected function _getForeignListVal(string $fieldName): string
    {
        return $this->foreignKeysData->get($fieldName);
    }

    /**
     * @deprecated
     * @param string $fieldName
     * @return bool
     */
    protected function _hasForeignListVal(string $fieldName): bool
    {
        return $this->foreignKeysData->has($fieldName);
    }

    /**
     * @deprecated
     * @param string $fieldName
     * @param string|array|null $value
     * @throws InvalidComponentException
     * @throws SchemaNotDefinedException
     */
    protected function _setForeignListVal(string $fieldName, $value = null): static
    {
        $this->foreignKeysData->set($fieldName, $value);
        return $this;
    }

    /**
     * @deprecated
     * @param string $fieldName
     * @param array $data
     * @return $this
     * @throws \Lkt\Factory\Schemas\Exceptions\InvalidItemDataAssignException
     */
    protected function _setForeignListWithData(string $fieldName, array $data = []): static
    {
        $this->foreignKeysData->set($fieldName, $data);
        return $this;
    }

    /**
     * @deprecated
     * @param string $fieldName
     * @param array $value
     * @return $this
     * @throws InvalidComponentException
     * @throws SchemaNotDefinedException
     */
    protected function _removeForeignListIds(string $fieldName, array $value = []): static
    {
        $this->foreignKeysData->removeIds($fieldName, $value);
        return $this;
    }
}