<?php

namespace Lkt\Factory\Schemas\Traits;

use Lkt\Factory\Schemas\Values\BooleanValue;

trait FieldWithSoftTypedOptionTrait
{
    protected ?BooleanValue $softTyped = null;

    /**
     * @param bool $softTyped
     * @return $this
     */
    final public function setIsSoftTyped(bool $softTyped = true): static
    {
        $this->softTyped = new BooleanValue($softTyped);
        return $this;
    }

    final public function isSoftTyped(): bool
    {
        if ($this->softTyped instanceof BooleanValue) {
            return $this->softTyped->getValue();
        }
        return false;
    }
}