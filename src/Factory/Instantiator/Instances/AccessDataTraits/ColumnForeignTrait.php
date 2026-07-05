<?php

namespace Lkt\Factory\Instantiator\Instances\AccessDataTraits;

use Lkt\Debug\VarDumper;
use Lkt\Factory\Instance\Traits\ItemWithForeignKeyDataTrait;
use Lkt\Factory\Instantiator\ComponentId;
use Lkt\Factory\Instantiator\Helpers\UpdatedRelatedDataProcessor;
use Lkt\Factory\Instantiator\Instances\AbstractInstance;
use Lkt\Factory\Instantiator\Instantiator;
use Lkt\Factory\Schemas\Exceptions\InvalidSchemaAppClassException;
use Lkt\Factory\Schemas\Exceptions\SchemaNotDefinedException;
use Lkt\Factory\Schemas\Schema;

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