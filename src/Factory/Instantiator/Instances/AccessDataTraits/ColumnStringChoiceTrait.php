<?php

namespace Lkt\Factory\Instantiator\Instances\AccessDataTraits;

use Lkt\Factory\Instance\Traits\ItemWithStringDataTrait;
use Lkt\Factory\Instantiator\Conversions\RawResultsToInstanceConverter;
use Lkt\Factory\Instantiator\Exceptions\InvalidStringChoiceValueException;
use Lkt\Factory\Schemas\Fields\StringChoiceField;
use Lkt\Factory\Schemas\Schema;

trait ColumnStringChoiceTrait
{
    use ItemWithStringDataTrait;

    protected function _getStringChoiceVal(string $fieldName): string
    {
        return $this->stringData->get($fieldName);
    }

    protected function _hasStringChoiceVal(string $fieldName): bool
    {
        return $this->stringData->has($fieldName);
    }

    protected function _stringChoiceIn(string $fieldName, array $values): bool
    {
        return $this->stringData->in($fieldName, $values);
    }

    protected function _stringChoiceEqual(string $fieldName, string|object $compared): bool
    {
        return $this->stringData->equal($fieldName, $compared);
    }

    /**
     * @note Object type value it's intended to match with an enum object
     */
    protected function _setStringChoiceVal(string $fieldName, string|array|object $value = null): static
    {
        $this->stringData->set($fieldName, $value);
        return $this;
    }
}