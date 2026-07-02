<?php

namespace Lkt\Factory\Instantiator\Instances\AccessDataTraits;

use Lkt\Factory\Instance\Traits\ItemWithBooleanDataTrait;
use Lkt\Factory\Instantiator\Conversions\RawResultsToInstanceConverter;
use Lkt\Factory\Schemas\Exceptions\InvalidComponentException;
use Lkt\Factory\Schemas\Exceptions\SchemaNotDefinedException;

trait ColumnBooleanTrait
{
    use ItemWithBooleanDataTrait;

    /**
     * @param string $fieldName
     * @return bool
     */
    protected function _getBooleanVal(string $fieldName): bool
    {
        return $this->booleanData->get($fieldName);

        if (isset($this->UPDATED[$fieldName])) {
            return $this->UPDATED[$fieldName];
        }
        return $this->DATA[$fieldName] === true;
    }

    /**
     * @param string $fieldName
     * @param bool $value
     * @return void
     * @throws InvalidComponentException
     * @throws SchemaNotDefinedException
     */
    protected function _setBooleanVal(string $fieldName, bool $value = false): static
    {
        $this->booleanData->set($fieldName, $value);
        return $this;

        $converter = new RawResultsToInstanceConverter(static::COMPONENT, [
            $fieldName => $value,
        ], false);

        foreach ($converter->parse() as $key => $value) {
            if ($this->DATA[$key] !== $value) $this->UPDATED[$key] = $value;
        }
        return $this;
    }
}