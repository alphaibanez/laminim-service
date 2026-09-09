<?php

namespace Lkt\Factory\Schemas\Fields;

use Lkt\Factory\Fields\Interfaces\NonRelationalField;
use Lkt\Factory\Fields\Traits\FieldWithEmptyDataModeTrait;
use Lkt\Factory\Fields\Traits\FieldWithInvalidDataModeTrait;
use Lkt\Factory\Fields\Traits\FieldWithMultipleOptionTrait;
use Lkt\Factory\Fields\Traits\FieldWithNullOptionTrait;

class FloatField extends AbstractField implements NonRelationalField
{
    use FieldWithNullOptionTrait,
        FieldWithMultipleOptionTrait,
        FieldWithEmptyDataModeTrait,
        FieldWithInvalidDataModeTrait;

    protected float|null $minValue = null;

    public function setMinValue(float $val): static
    {
        $this->minValue = $val;
        return $this;
    }

    public function getMinValue(): float|null
    {
        return $this->minValue;
    }
}