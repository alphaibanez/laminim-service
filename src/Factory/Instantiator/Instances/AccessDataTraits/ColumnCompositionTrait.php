<?php

namespace Lkt\Factory\Instantiator\Instances\AccessDataTraits;

use Lkt\Factory\Schemas\Exceptions\InvalidComponentException;
use Lkt\Factory\Schemas\Exceptions\SchemaNotDefinedException;

trait ColumnCompositionTrait
{
    protected function _getCompositionAdditionalData(array $additionalData = [], string $fieldName = null)
    {
        return $this->composedData->prepareAdditionalData((string)$fieldName, $additionalData);
    }

    protected function _getCompositionInstance(string $composedComponent, array $additionalData = []): mixed
    {
        return $this->composedData->getItem($composedComponent, $additionalData);
    }

    /**
     * @param string $composedComponent
     * @param string $fieldName
     * @return mixed
     * @throws SchemaNotDefinedException
     */
    protected function _getCompositionVal(string $composedComponent, string $fieldName, array $additionalData = []): mixed
    {
        return $this->composedData->get($composedComponent, $fieldName, $additionalData);
    }

    /**
     * @param string $component
     * @param string $composedComponent
     * @param string $fieldName
     * @param mixed $value
     * @return $this
     * @throws InvalidComponentException
     * @throws SchemaNotDefinedException
     */
    protected function _setCompositionVal(string $composedComponent, string $fieldName, mixed $value, array $additionalData = []): static
    {
        $this->composedData->set($composedComponent, $fieldName, $value, $additionalData);
        return $this;
    }

    /**
     * @param string $composedComponent
     * @param string $fieldName
     * @return bool
     * @throws InvalidComponentException
     * @throws SchemaNotDefinedException
     */
    protected function _hasCompositionVal(string $composedComponent, string $fieldName, array $additionalData = []): bool
    {
        return $this->composedData->has($composedComponent, $fieldName, $additionalData);
    }
}