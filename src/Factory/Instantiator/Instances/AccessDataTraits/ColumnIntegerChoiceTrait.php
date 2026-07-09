<?php

namespace Lkt\Factory\Instantiator\Instances\AccessDataTraits;

trait ColumnIntegerChoiceTrait
{
    protected function _getIntegerChoiceVal(string $fieldName): int|array
    {
        $field = $this->getSchema()->getField($fieldName);
        if ($field->isMultiple()) {
            return $this->multipleIntegerData->get($fieldName) ?? [];
        }
        return (int)$this->integerData->get($fieldName);
    }

    protected function _hasIntegerChoiceVal(string $fieldName): bool
    {
        $field = $this->getSchema()->getField($fieldName);
        if ($field->isMultiple()) {
            return $this->multipleIntegerData->has($fieldName);
        }
        return $this->integerData->has($fieldName);
    }

    protected function _integerChoiceIn(string $fieldName, array $values): bool
    {
        $field = $this->getSchema()->getField($fieldName);
        if ($field->isMultiple()) {
            return $this->multipleIntegerData->in($fieldName, $values);
        }
        return $this->integerData->in($fieldName, $values);
    }

    protected function _integerChoiceEqual(string $fieldName, int|array|object $compared): bool
    {
        $field = $this->getSchema()->getField($fieldName);
        if ($field->isMultiple()) {
            return $this->multipleIntegerData->equal($fieldName, $compared);
        }
        return $this->integerData->equal($fieldName, $compared);
    }

    /**
     * @note Object type value it's intended to match with an enum object
     */
    protected function _setIntegerChoiceVal(string $fieldName, int|array|object $value = null): static
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