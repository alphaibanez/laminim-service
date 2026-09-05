<?php

namespace Lkt\Factory\Schemas\Traits;

use Lkt\Factory\Schemas\Values\StringValue;

trait FieldWithJsonI18nStorageTrait
{
    protected bool $storeAsI18nJson = false;
    protected ?StringValue $fixedLangKey = null;

    /**
     * @deprecated use ::i18n constructor instead
     * @param bool $allow
     * @return \Lkt\Factory\Schemas\Fields\StringField|FieldWithJsonI18nStorageTrait
     */
    final public function setIsI18nJson(bool $allow = true): self
    {
        $this->storeAsI18nJson = $allow;
        return $this;
    }

    final public function isI18nJson(): bool
    {
        return $this->storeAsI18nJson;
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