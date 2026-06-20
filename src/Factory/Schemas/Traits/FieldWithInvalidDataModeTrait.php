<?php

namespace Lkt\Factory\Schemas\Traits;

use Lkt\Factory\Instance\Enums\InvalidDataMode;

trait FieldWithInvalidDataModeTrait
{
    protected InvalidDataMode $invalidDataMode = InvalidDataMode::CastToType;

    public function getInvalidDataMode(): InvalidDataMode
    {
        return $this->invalidDataMode;
    }

    public function setInvalidDataMode(InvalidDataMode $mode): static
    {
        $this->invalidDataMode = $mode;
        return $this;
    }
}