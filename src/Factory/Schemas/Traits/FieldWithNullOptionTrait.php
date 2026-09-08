<?php

namespace Lkt\Factory\Schemas\Traits;

trait FieldWithNullOptionTrait
{
    protected bool|null $nullable = null;

    final public function setNullable(bool $allow = true): self
    {
        $this->nullable = $allow;
        return $this;
    }

    final public function isNullable(): bool
    {
        return $this->nullable === true;
    }
}