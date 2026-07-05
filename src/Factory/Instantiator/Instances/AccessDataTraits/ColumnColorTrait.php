<?php

namespace Lkt\Factory\Instantiator\Instances\AccessDataTraits;

use Lkt\Factory\Instance\Traits\ItemWithColorDataTrait;
use Lkt\Factory\Schemas\Exceptions\InvalidComponentException;
use Lkt\Factory\Schemas\Exceptions\SchemaNotDefinedException;
use function Lkt\Tools\Color\hexToDec;

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
        $r = $this->colorData->get($fieldName);

        $r = hexToDec($r);
        if ($opacity !== null) {
            $r[] = $opacity;
        }

        return $r;
    }

    /**
     * @param string $fieldName
     * @param float|null $opacity
     * @return string
     */
    protected function _getColorRgbStringVal(string $fieldName, float $opacity = null): string
    {
        $color = $this->_getColorRgbVal($fieldName, $opacity);
        $base = 'rgb';
        if (count($color) === 4) {
            $base .= 'a';
        }

        $r = implode(',', $color);

        return "{$base}($r)";
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