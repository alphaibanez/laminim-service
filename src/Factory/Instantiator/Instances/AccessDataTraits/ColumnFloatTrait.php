<?php

namespace Lkt\Factory\Instantiator\Instances\AccessDataTraits;

use Lkt\Factory\Instantiator\Conversions\RawResultsToInstanceConverter;
use Lkt\Factory\Instantiator\SystemConnections\NumberFormatter;
use Lkt\Factory\Schemas\Exceptions\InvalidComponentException;
use Lkt\Factory\Schemas\Exceptions\SchemaNotDefinedException;
use Lkt\Factory\Schemas\Fields\FloatField;
use Lkt\Factory\Schemas\Schema;

trait ColumnFloatTrait
{
    /**
     * @param string $field
     * @return float|float[]
     */
    protected function _getFloatVal(string $field): float|array
    {
        $schema = Schema::get(static::COMPONENT);
        /** @var FloatField $field */
        $fieldIns = $schema->getField($field);

        if (isset($this->UPDATED[$field])) return $this->UPDATED[$field];
        if (isset($this->DATA[$field])) return $this->DATA[$field];
        if ($fieldIns->isMultiple()) return [];
        return 0;
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
        $checkField = 'has' . ucfirst($fieldName);
        if (isset($this->UPDATED[$checkField])) return $this->UPDATED[$checkField];
        return $this->DATA[$checkField] === true;
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
        $converter = new RawResultsToInstanceConverter(static::COMPONENT, [
            $fieldName => $value,
        ], false);

        foreach ($converter->parse() as $key => $value) $this->UPDATED[$key] = $value;
        return $this;
    }
}