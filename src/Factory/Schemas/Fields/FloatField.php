<?php

namespace Lkt\Factory\Schemas\Fields;

use Lkt\Factory\Schemas\Traits\FieldWithMultipleOptionTrait;
use Lkt\Factory\Schemas\Traits\FieldWithNullOptionTrait;

class FloatField extends AbstractField
{
    const TYPE = 'float';

    use FieldWithNullOptionTrait,
        FieldWithMultipleOptionTrait;

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