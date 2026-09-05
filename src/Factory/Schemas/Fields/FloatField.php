<?php

namespace Lkt\Factory\Schemas\Fields;

use Lkt\Factory\Fields\Interfaces\NonRelationalField;
use Lkt\Factory\Schemas\Traits\FieldWithEmptyDataModeTrait;
use Lkt\Factory\Schemas\Traits\FieldWithInvalidDataModeTrait;
use Lkt\Factory\Schemas\Traits\FieldWithMultipleOptionTrait;
use Lkt\Factory\Schemas\Traits\FieldWithNullOptionTrait;

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