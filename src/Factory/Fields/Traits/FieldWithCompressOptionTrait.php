<?php

namespace Lkt\Factory\Fields\Traits;

trait FieldWithCompressOptionTrait
{
    protected bool|null $compress = null;

    final public function setIsCompressed(bool $compress = true): static
    {
        $this->compress = $compress;
        return $this;
    }

    final public function isCompressed(): bool
    {
        return $this->compress === true;
    }
}