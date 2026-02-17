<?php

namespace Lkt\Factory\Schemas\Traits;

trait FieldWithOnReadIncludeOptionsTrait
{

    protected bool $onReadIncludeOptions = false;

    public function setOnReadIncludeOptions(bool $value = true): static
    {
        $this->onReadIncludeOptions = $value;
        return $this;
    }

    public function hasOnReadIncludeOptions(): bool
    {
        return $this->onReadIncludeOptions;
    }
}