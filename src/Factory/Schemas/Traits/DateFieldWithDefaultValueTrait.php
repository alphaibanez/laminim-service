<?php

namespace Lkt\Factory\Schemas\Traits;

trait DateFieldWithDefaultValueTrait
{
    protected bool $defaultCurrentTimestamp = false;

    public function setCurrentTimeStampAsDefaultValue(bool $enable = true): static
    {
        $this->defaultCurrentTimestamp = $enable;
        return $this;
    }
}