<?php

namespace Lkt\Factory\Instantiator\Instances\AccessDataTraits;

use Lkt\Factory\Instantiator\Conversions\RawResultsToInstanceConverter;
use Lkt\Factory\Instantiator\Exceptions\InvalidIntegerChoiceValueException;
use Lkt\Factory\Schemas\Fields\IntegerChoiceField;
use Lkt\Factory\Schemas\Schema;

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


        $schema = Schema::get(static::COMPONENT);
        /** @var IntegerField $field */
        $field = $schema->getField($fieldName);

        $comparedValues = array_map(function ($v){
            $c = $v;
            if (is_object($v) && property_exists($v, 'value') && isset($v->value)) {
                $c = $v->value;
            }
            return $c;

        }, $values);

        if ($field->isMultiple()) {
            /** @var int[] $value */
            $value = $this->_getIntegerChoiceVal($fieldName);
            if (count($value) === 0) return false;

            $r = true;
            foreach ($value as $val) {
                $r = $r && in_array($val, $comparedValues, true);
            }

            return $r;
        }

        $value = $this->_getIntegerChoiceVal($fieldName);
        return in_array($value, $comparedValues, true);
    }

    protected function _integerChoiceEqual(string $fieldName, int|array|object $compared): bool
    {
        $field = $this->getSchema()->getField($fieldName);
        if ($field->isMultiple()) {
            return $this->multipleIntegerData->equal($fieldName, $compared);
        }
        return $this->integerData->equal($fieldName, $compared);


        $schema = Schema::get(static::COMPONENT);
        /** @var IntegerField $field */
        $field = $schema->getField($fieldName);

        if ($field->isMultiple()) {
            /** @var int[] $value */
            $value = $this->_getIntegerChoiceVal($fieldName);

            $comparedValues = array_map(function ($v){
                $c = $v;
                if (is_object($v) && property_exists($v, 'value') && isset($v->value)) {
                    $c = $v->value;
                }
                return $c;

            }, $compared);

            return count($value) === count($comparedValues)
                && count(array_intersect($value, $comparedValues)) === 0;
        }

        $c = $compared;
        if (is_object($compared) && property_exists($compared, 'value') && isset($compared->value)) {
            $c = $compared->value;
        }

        $value = $this->_getIntegerChoiceVal($fieldName);
        return $value === $c;
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