<?php

namespace Lkt\Factory\Schemas\Traits;

use Lkt\Factory\Schemas\Values\BooleanValue;

trait FieldWithNullOptionTrait
{
    protected ?BooleanValue $nullable = null;

    final public function setNullable(bool $allow = true): self
    {
        $this->nullable = new BooleanValue($allow);
        return $this;
    }

    final public function isNullable(): bool
    {
        if ($this->nullable instanceof BooleanValue) {
            return $this->nullable->getValue();
        }
        return false;
    }
}