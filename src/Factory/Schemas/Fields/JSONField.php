<?php

namespace Lkt\Factory\Schemas\Fields;

use Lkt\Factory\Schemas\Traits\FieldWithCompressOptionTrait;
use Lkt\Factory\Schemas\Traits\FieldWithNullOptionTrait;
use Lkt\Factory\Schemas\Values\BooleanValue;

class JSONField extends AbstractField
{
    const TYPE = 'json';

    use FieldWithCompressOptionTrait,
        FieldWithNullOptionTrait;

    protected ?BooleanValue $assoc = null;

    public function setIsAssoc(bool $assoc = true): static
    {
        $this->assoc = new BooleanValue($assoc);
        return $this;
    }

    public function isAssoc(bool $assoc = true): bool
    {
        if ($this->assoc instanceof BooleanValue) {
            return $this->assoc->getValue();
        }
        return false;
    }

    protected ?BooleanValue $storeAsI18nJson = null;

    final public function setIsI18nJson(bool $allow = true): self
    {
        $this->storeAsI18nJson = new BooleanValue($allow);
        return $this;
    }

    final public function isI18nJson(): bool
    {
        if ($this->storeAsI18nJson instanceof BooleanValue) {
            return $this->storeAsI18nJson->getValue();
        }
        return false;
    }
}