<?php

namespace Lkt\Factory\Instantiator\Instances\AccessDataTraits;

use Lkt\Factory\Schemas\Exceptions\InvalidComponentException;
use Lkt\Factory\Schemas\Exceptions\InvalidSchemaAppClassException;
use Lkt\Factory\Schemas\Exceptions\SchemaNotDefinedException;

trait ColumnPivotTrait
{
    protected array $PIVOT_SORT = [];

    /**
     * @param string $column
     * @return array
     * @throws InvalidComponentException
     * @throws InvalidSchemaAppClassException
     * @throws SchemaNotDefinedException
     */
    protected function _getPivotVal(string $column): array
    {
        return (array)$this->pivotData->getItems($column);
    }

    /**
     * @param string $column
     * @return bool
     * @throws InvalidComponentException
     * @throws InvalidSchemaAppClassException
     * @throws SchemaNotDefinedException
     */
    protected function _hasPivotVal(string $column = ''): bool
    {
        return $this->pivotData->has($column);
    }
}