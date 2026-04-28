<?php

namespace Lkt\Factory\Schemas\Traits;

trait DateFieldWithDefaultValueTrait
{
    protected bool $defaultCurrentTimestamp = false;
    protected bool $onUpdateCurrentTimestamp = false;

    public function setCurrentTimeStampAsDefaultValue(bool $enable = true): static
    {
        $this->defaultCurrentTimestamp = $enable;
        return $this;
    }

    public function setCurrentTimeStampOnUpdate(bool $enable = true): static
    {
        $this->onUpdateCurrentTimestamp = $enable;
        return $this;
    }

    public function hasToSetCurrentTimeStampOnUpdate(): bool
    {
        return $this->onUpdateCurrentTimestamp;
    }
}