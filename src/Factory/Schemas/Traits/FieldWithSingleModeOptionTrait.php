<?php

namespace Lkt\Factory\Schemas\Traits;

use Lkt\Factory\Schemas\Values\BooleanValue;

trait FieldWithSingleModeOptionTrait
{
    protected ?BooleanValue $singleMode = null;

    /**
     * @return FieldWithSingleModeOptionTrait
     */
    final public function setSingleMode(bool $isSingleMode = true): static
    {
        $this->singleMode = new BooleanValue($isSingleMode);
        return $this;
    }

    final public function isSingleMode(): bool
    {
        if ($this->singleMode instanceof BooleanValue) {
            return $this->singleMode->getValue();
        }
        return false;
    }
}