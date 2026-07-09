<?php

namespace Lkt\Factory\Instantiator\Instances\AccessDataTraits;

use Lkt\Factory\Instance\Traits\ItemWithFloatDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithMultipleFloatDataTrait;
use Lkt\Factory\Instantiator\SystemConnections\NumberFormatter;
use Lkt\Factory\Schemas\Exceptions\InvalidComponentException;
use Lkt\Factory\Schemas\Exceptions\SchemaNotDefinedException;

trait ColumnFloatTrait
{
    use ItemWithFloatDataTrait,
        ItemWithMultipleFloatDataTrait;
    /**
     * @param string $field
     * @return float|float[]
     */
    protected function _getFloatVal(string $field): float|array
    {
        $fieldName = $field;
        $field = $this->getSchema()->getField($fieldName);
        if ($field->isMultiple()) {
            return (array)$this->multipleFloatData->get($fieldName);
        }
        return (float)$this->floatData->get($fieldName);
    }

    /**
     * @param string $fieldName
     * @return string
     */
    protected function _getFloatFormattedVal(string $fieldName): string|array
    {
        $formatter = NumberFormatter::getDecimalNumberFormatter();
        return $formatter->format($this->_getFloatVal($fieldName));
    }

    /**
     * @param string $fieldName
     * @return bool
     */
    protected function _hasFloatVal(string $fieldName): bool
    {
        $field = $this->getSchema()->getField($fieldName);
        if ($field->isMultiple()) {
            return $this->multipleFloatData->has($fieldName);
        }
        return $this->floatData->has($fieldName);
    }

    /**
     * @param string $fieldName
     * @param float|null $value
     * @return void
     * @throws InvalidComponentException
     * @throws SchemaNotDefinedException
     */
    protected function _setFloatVal(string $fieldName, float|array $value = null): static
    {
        $field = $this->getSchema()->getField($fieldName);
        if ($field->isMultiple()) {
            $this->multipleFloatData->set($fieldName, $value);
        } else {
            $this->floatData->set($fieldName, $value);
        }
        return $this;
    }
}