<?php

namespace Lkt\Factory\Schemas\Traits;

trait FieldWithMandatoryOptionTrait
{
    protected bool $mandatory = false;

    /**
     * @param $where
     * @return $this
     */
    final public function setIsMandatory(bool $enabled = true): static
    {
        $this->mandatory = $enabled;
        return $this;
    }

    final public function isMandatory(): bool
    {
        return $this->mandatory;
    }
}