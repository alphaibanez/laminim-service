<?php

namespace Lkt\Factory\Instantiator\Instances\AccessDataTraits;

use Lkt\Factory\Instance\Traits\ItemWithStringDataTrait;
use Lkt\Factory\Instantiator\Conversions\RawResultsToInstanceConverter;
use Lkt\Factory\Instantiator\Exceptions\InvalidStringChoiceValueException;
use Lkt\Factory\Schemas\Fields\StringChoiceField;
use Lkt\Factory\Schemas\Schema;

trait ColumnStringChoiceTrait
{
    use ItemWithStringDataTrait;

    protected function _getStringChoiceVal(string $fieldName): string
    {
        return $this->stringData->get($fieldName);

        if (isset($this->UPDATED[$fieldName])) {
            return $this->UPDATED[$fieldName];
        }
        return trim($this->DATA[$fieldName]);
    }

    protected function _hasStringChoiceVal(string $fieldName): bool
    {
        return $this->stringData->has($fieldName);

        $checkField = 'has' . ucfirst($fieldName);
        if (isset($this->UPDATED[$checkField])) {
            return $this->UPDATED[$checkField];
        }
        return $this->DATA[$checkField] === true;
    }

    protected function _stringChoiceIn(string $fieldName, array $values): bool
    {
        return $this->stringData->in($fieldName, $values);

        $value = $this->_getStringChoiceVal($fieldName);
        return in_array($value, $values, true);
    }

    protected function _stringChoiceEqual(string $fieldName, string|object $compared): bool
    {
        return $this->stringData->equal($fieldName, $compared);

        $c = $compared;
        if (is_object($compared) && property_exists($compared, 'value') && isset($compared->value)) {
            $c = $compared->value;
        }

        $value = $this->_getStringChoiceVal($fieldName);
        return $value === $c;
    }

    /**
     * @note Object type value it's intended to match with an enum object
     */
    protected function _setStringChoiceVal(string $fieldName, string|array|object $value = null): static
    {
        $this->stringData->set($fieldName, $value);
        return $this;

        $schema = Schema::get(static::COMPONENT);
        /** @var StringChoiceField $field */
        $field = $schema->getField($fieldName);
        $availableOptions = $field->getAllowedOptions();

        if (is_array($value)) {
            foreach ($value as $val) {

                $v = $val;
                if (is_object($v) && isset($v->value)) {
                    $v = $v->value;
                }

                if (!in_array($v, $availableOptions, true)) {
                    throw InvalidStringChoiceValueException::getInstance($v, $fieldName, static::COMPONENT);
                }
            }

        } else {

            if (is_object($value) && property_exists($value, 'value') && isset($value->value)) {
                $value = $value->value;
            }

            if (!in_array($value, $availableOptions, true)) {
                throw InvalidStringChoiceValueException::getInstance($value, $fieldName, static::COMPONENT);
            }
        }

        $converter = new RawResultsToInstanceConverter(static::COMPONENT, [
            $fieldName => $value,
        ], false);

        foreach ($converter->parse() as $key => $value) {
            if ($this->DATA[$key] !== $value) $this->UPDATED[$key] = $value;
        }
        return $this;
    }
}