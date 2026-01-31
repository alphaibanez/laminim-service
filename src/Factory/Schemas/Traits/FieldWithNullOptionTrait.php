<?php

namespace Lkt\Factory\Schemas\Traits;

use Lkt\Factory\Schemas\Values\BooleanValue;

trait FieldWithNullOptionTrait
{
    protected ?BooleanValue $allowNull = null;

    final public function setNullable(bool $allow = true): self
    {
        $this->allowNull = new BooleanValue($allow);
        return $this;
    }

    final public function canBeNull(): bool
    {
        if ($this->allowNull instanceof BooleanValue) {
            return $this->allowNull->getValue();
        }
        return false;
    }
}