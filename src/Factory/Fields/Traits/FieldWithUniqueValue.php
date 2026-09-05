<?php

namespace Lkt\Factory\Fields\Traits;

trait FieldWithUniqueValue
{
    protected bool $unique = false;

    public function setIsUnique(bool $isUnique = true): static
    {
        $this->unique = $isUnique;
        return $this;
    }

    public function isUnique(): bool
    {
        return $this->unique;
    }
}