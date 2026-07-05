<?php

namespace Lkt\Factory\Instantiator\Instances\AccessDataTraits;

use Lkt\Factory\Instance\Traits\ItemWithBooleanDataTrait;
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
        return (bool)$this->booleanData->get($fieldName);
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
    }
}