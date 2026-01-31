<?php

namespace Lkt\Factory\Schemas\Traits;

use Lkt\Factory\Schemas\Values\BooleanValue;

trait FieldWithCompressOptionTrait
{
    protected ?BooleanValue $compress = null;

    final public function setIsCompressed(bool $compress = true): static
    {
        $this->compress = new BooleanValue($compress);
        return $this;
    }

    final public function isCompressed(): bool
    {
        if ($this->compress instanceof BooleanValue) {
            return $this->compress->getValue();
        }
        return false;
    }
}