<?php

namespace Lkt\Factory\Schemas\Traits;

use Lkt\Factory\Schemas\Values\BooleanValue;
use Lkt\Factory\Schemas\Values\StringValue;

trait FieldWithJsonI18nStorageTrait
{
    protected ?BooleanValue $storeAsI18nJson = null;
    protected ?StringValue $fixedLangKey = null;

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

    final public function setFixedLangKey(string $lang): self
    {
        $this->fixedLangKey = new StringValue($lang);
        return $this;
    }

    final public function hasFixedLangKey(): bool
    {
        if ($this->fixedLangKey instanceof StringValue) {
            return $this->fixedLangKey->getValue() !== '';
        }
        return false;
    }

    final public function getFixedLangKey(): string
    {
        if ($this->fixedLangKey instanceof StringValue) {
            return $this->fixedLangKey->getValue();
        }
        return '';
    }
}