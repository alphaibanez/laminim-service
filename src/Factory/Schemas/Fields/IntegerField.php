<?php

namespace Lkt\Factory\Schemas\Fields;

use Lkt\Factory\Fields\Traits\FieldWithChoiceOptionTrait;
use Lkt\Factory\Fields\Traits\FieldWithEmptyDataModeTrait;
use Lkt\Factory\Fields\Traits\FieldWithInvalidDataModeTrait;
use Lkt\Factory\Fields\Traits\FieldWithMultipleOptionTrait;
use Lkt\Factory\Fields\Traits\FieldWithNullOptionTrait;
use Lkt\Factory\Fields\Traits\FieldWithPrefabRoleTrait;

class IntegerField extends AbstractField
{
    use FieldWithNullOptionTrait,
        FieldWithMultipleOptionTrait,
        FieldWithInvalidDataModeTrait,
        FieldWithEmptyDataModeTrait,
        FieldWithChoiceOptionTrait,
        FieldWithPrefabRoleTrait;

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