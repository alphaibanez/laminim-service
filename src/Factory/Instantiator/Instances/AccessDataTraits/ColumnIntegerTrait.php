<?php

namespace Lkt\Factory\Instantiator\Instances\AccessDataTraits;

use Lkt\Factory\Instance\Traits\ItemWithForeignKeyDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithIntegerDataTrait;
use Lkt\Factory\Schemas\Fields\ForeignKeyField;

trait ColumnIntegerTrait
{
    use ItemWithIntegerDataTrait,
        ItemWithForeignKeyDataTrait;

    protected function _getIntegerVal(string $fieldName): int|array|null
    {
        $field = $this->getSchema()->getField($fieldName);
        if ($field instanceof ForeignKeyField) {
            return (int)$this->foreignKeyData->get($field->getName());
        }
        if ($field->isMultiple()) {
            return (array)$this->multipleIntegerData->get($fieldName);
        }
        return (int)$this->integerData->get($fieldName);
    }

    protected function _hasIntegerVal(string $fieldName): bool
    {
        $field = $this->getSchema()->getField($fieldName);
        if ($field->isMultiple()) {
            return $this->multipleIntegerData->has($fieldName);
        }
        return $this->integerData->has($fieldName);
    }

    protected function _setIntegerVal(string $fieldName, int|array $value = null): static
    {
        $field = $this->getSchema()->getField($fieldName);
        if ($field->isMultiple()) {
            $this->multipleIntegerData->set($fieldName, $value);
        } else {
            $this->integerData->set($fieldName, $value);
        }
        return $this;
    }
}