<?php

namespace Lkt\Factory\Schemas\Traits;

trait FieldWithSoftTypedOptionTrait
{
    protected bool $softTyped = false;

    /**
     * @param bool $softTyped
     * @return $this
     */
    final public function setIsSoftTyped(bool $softTyped = true): static
    {
        $this->softTyped = $softTyped;
        return $this;
    }

    final public function isSoftTyped(): bool
    {
        return $this->softTyped;
    }
}