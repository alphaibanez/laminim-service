<?php

namespace Lkt\Factory\Schemas\Traits;

trait FieldWithSingleModeOptionTrait
{
    protected bool $singleMode = false;

    /**
     * @deprecated use ::single constructor
     * @return FieldWithSingleModeOptionTrait
     */
    final public function setSingleMode(bool $isSingleMode = true): static
    {
        $this->singleMode = $isSingleMode;
        return $this;
    }

    final public function isSingleMode(): bool
    {
        return $this->singleMode;
    }
}