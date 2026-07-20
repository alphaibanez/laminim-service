<?php

namespace Lkt\Factory\Instantiator\Instances\AccessDataTraits;

use Lkt\Factory\Instance\Traits\ItemWithForeignKeyDataTrait;
use Lkt\Factory\Instantiator\Instances\AbstractInstance;
use Lkt\Factory\Schemas\Exceptions\InvalidSchemaAppClassException;
use Lkt\Factory\Schemas\Exceptions\SchemaNotDefinedException;

trait ColumnForeignTrait
{
    use ItemWithForeignKeyDataTrait;

    /**
     * @param string $fieldName
     * @return AbstractInstance|null
     * @throws InvalidSchemaAppClassException
     * @throws SchemaNotDefinedException
     */
    protected function _getForeignVal($type = '', $id = 0, string $fieldName = ''): ?AbstractInstance
    {
        return $this->foreignKeyData->getItem($fieldName);
    }

    /**
     * @param string $fieldName
     * @return bool
     * @throws InvalidSchemaAppClassException
     * @throws SchemaNotDefinedException
     */
    protected function _hasForeignVal($type = '', $id = 0, string $fieldName = ''): bool
    {
        return $this->foreignKeyData->has($fieldName);
    }
}