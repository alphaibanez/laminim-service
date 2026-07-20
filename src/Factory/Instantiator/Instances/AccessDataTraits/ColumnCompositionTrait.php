<?php

namespace Lkt\Factory\Instantiator\Instances\AccessDataTraits;

use Lkt\Factory\Instance\Enums\RetrieveDataMode;
use Lkt\Factory\Schemas\Exceptions\InvalidComponentException;
use Lkt\Factory\Schemas\Exceptions\SchemaNotDefinedException;

trait ColumnCompositionTrait
{
    protected array $COMPOSED_DATA = [];

    protected function _getCompositionAdditionalData(array $additionalData = [], string $fieldName = null, mixed $reflectedInstance = null, string $reflectedMethod = null)
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
        $ins = $this->composedData->getItem($composedComponent, $additionalData);
        if (!$ins) return null;

        return $ins->retrieveValue($fieldName, $additionalData, RetrieveDataMode::Item);
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
        $ins = $this->composedData->getItem($composedComponent, $additionalData);
        if (!$ins) return $this;

        $ins->assignValue($fieldName, $value);
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
        $ins = $this->composedData->getItem($composedComponent, $additionalData);
        if (!$ins) return false;

        return $ins->hasAssignedValue($fieldName);
    }
}