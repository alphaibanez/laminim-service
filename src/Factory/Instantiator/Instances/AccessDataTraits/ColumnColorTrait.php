<?php

namespace Lkt\Factory\Instantiator\Instances\AccessDataTraits;

use Lkt\Factory\Instance\Traits\ItemWithColorDataTrait;
use Lkt\Factory\Schemas\Exceptions\InvalidComponentException;
use Lkt\Factory\Schemas\Exceptions\SchemaNotDefinedException;

trait ColumnColorTrait
{
    use ItemWithColorDataTrait;

    /**
     * @param string $fieldName
     * @return string
     */
    protected function _getColorVal(string $fieldName): string
    {
        return $this->colorData->get($fieldName);
    }

    /**
     * @param string $fieldName
     * @param float|null $opacity
     * @return array
     */
    protected function _getColorRgbVal(string $fieldName, float $opacity = null): array
    {
        return $this->colorData->getRGBA($fieldName, $opacity);
    }

    /**
     * @param string $fieldName
     * @param float|null $opacity
     * @return string
     */
    protected function _getColorRgbStringVal(string $fieldName, float $opacity = null): string
    {
        return $this->colorData->getRGBAString($fieldName, $opacity);
    }

    /**
     * @param string $fieldName
     * @return bool
     */
    protected function _hasColorVal(string $fieldName): bool
    {
        return $this->colorData->has($fieldName);
    }

    /**
     * @param string $fieldName
     * @param $value
     * @throws InvalidComponentException
     * @throws SchemaNotDefinedException
     */
    protected function _setColorVal(string $fieldName, $value = null): static
    {
        $this->colorData->set($fieldName, $value);
        return $this;
    }
}