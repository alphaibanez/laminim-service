<?php

namespace Lkt\Factory\Instantiator\Instances\AccessDataTraits;

use Lkt\Factory\Instance\Traits\ItemWithStringDataTrait;
use Lkt\Factory\Instantiator\Conversions\RawResultsToInstanceConverter;
use Lkt\Factory\Schemas\Exceptions\DuplicatedValueException;
use Lkt\Factory\Schemas\Fields\EmailField;
use Lkt\Factory\Schemas\Schema;

trait ColumnStringTrait
{
    use ItemWithStringDataTrait;

    protected function _getStringVal(string $fieldName): string
    {
        return (string)$this->stringData->get($fieldName);
    }

    protected function _hasStringVal(string $fieldName): bool
    {
        return $this->stringData->has($fieldName);
    }

    protected function _setStringVal(string $fieldName, string $value = null): static
    {
        $this->stringData->set($fieldName, $value);
        return $this;
    }
}