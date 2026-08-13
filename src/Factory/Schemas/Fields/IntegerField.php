<?php

namespace Lkt\Factory\Schemas\Fields;

use Lkt\Factory\Schemas\Traits\FieldWithEmptyDataModeTrait;
use Lkt\Factory\Schemas\Traits\FieldWithInvalidDataModeTrait;
use Lkt\Factory\Schemas\Traits\FieldWithMultipleOptionTrait;
use Lkt\Factory\Schemas\Traits\FieldWithNullOptionTrait;

class IntegerField extends AbstractField
{
    use FieldWithNullOptionTrait,
        FieldWithMultipleOptionTrait,
        FieldWithInvalidDataModeTrait,
        FieldWithEmptyDataModeTrait;

    protected int|null $minValue = null;

    public function setMinValue(int $val): static
    {
        $this->minValue = $val;
        return $this;
    }

    public function getMinValue(): int|null
    {
        return $this->minValue;
    }
}