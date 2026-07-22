<?php

namespace Lkt\Factory\Instantiator\Instances\AccessDataTraits;

use Carbon\Carbon;
use DateTime;
use Lkt\Factory\Instance\Traits\ItemWithDateDataTrait;

trait ColumnDateTimeTrait
{
    use ItemWithDateDataTrait;

    /**
     * @param string $fieldName
     * @return Carbon|null
     */
    protected function _getDateTimeVal(string $fieldName): ?Carbon
    {
        return $this->dateData->get($fieldName);
    }

    /**
     * @param string $fieldName
     * @param string|null $format
     * @return string
     */
    protected function _getDateTimeFormattedVal(string $fieldName, string $format = null): string
    {
        return $this->dateData->format($fieldName, $format);
    }

    /**
     * @param string $fieldName
     * @param string|null $format
     * @return string
     */
    protected function _getDateTimeFormattedIntlVal(string $fieldName, string $format = null): string
    {
        return trim($this->dateData->intlFormat($fieldName, $format));
    }

    /**
     * @param string $fieldName
     * @return bool
     */
    protected function _hasDateTimeVal(string $fieldName): bool
    {
        return $this->dateData->has($fieldName);
    }

    /**
     * @throws \Lkt\Factory\Schemas\Exceptions\InvalidComponentException
     * @throws \Lkt\Factory\Schemas\Exceptions\SchemaNotDefinedException
     */
    protected function _setDateTimeVal(string $fieldName, Carbon|DateTime|int|string|null $value = null): static
    {
        $this->dateData->set($fieldName, $value);
        return $this;
    }
}