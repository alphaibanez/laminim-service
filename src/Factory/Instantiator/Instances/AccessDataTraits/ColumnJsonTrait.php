<?php

namespace Lkt\Factory\Instantiator\Instances\AccessDataTraits;

use Lkt\Factory\Instance\Traits\ItemWithJSONDataTrait;
use Lkt\Factory\Schemas\Exceptions\InvalidComponentException;
use Lkt\Factory\Schemas\Exceptions\SchemaNotDefinedException;
use StdClass;

trait ColumnJsonTrait
{
    use ItemWithJSONDataTrait;

    /**
     * @param string $fieldName
     * @return array|StdClass
     * @throws InvalidComponentException
     * @throws SchemaNotDefinedException
     */
    protected function _getJsonVal(string $fieldName)
    {
        return $this->jsonData->get($fieldName);
    }

    /**
     * @param string $fieldName
     * @return bool
     */
    protected function _hasJsonVal(string $fieldName): bool
    {
        return $this->jsonData->has($fieldName);
    }

    /**
     * @param string $fieldName
     * @param $value
     * @throws InvalidComponentException
     * @throws SchemaNotDefinedException
     */
    protected function _setJsonVal(string $fieldName, $value = null): static
    {
        $this->jsonData->set($fieldName, $value);
        return $this;
    }
}