<?php

namespace Lkt\Factory\Schemas\Fields;

use Lkt\Factory\Schemas\Traits\FieldWithMultipleOptionTrait;
use Lkt\Factory\Schemas\Traits\FieldWithNullOptionTrait;

class IntegerField extends AbstractField
{
    const TYPE = 'integer';

    protected int|null $minValue = null;

    use FieldWithNullOptionTrait,
        FieldWithMultipleOptionTrait;

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