<?php

namespace Lkt\Factory\Fields\Traits;

use Lkt\Factory\Instance\Enums\TrimMode;

trait FieldWithTrimMode
{
    protected TrimMode $trimMode = TrimMode::Full;

    public function getTrimMode(): TrimMode
    {
        return $this->trimMode;
    }

    public function setTrimMode(TrimMode $mode): static
    {
        $this->trimMode = $mode;
        return $this;
    }
}