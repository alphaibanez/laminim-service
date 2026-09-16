<?php

namespace Lkt\Factory\Fields\Traits;

trait FieldWithJsonI18nStorageTrait
{
    protected bool $storeAsI18nJson = false;
    protected string|null $fixedLangKey = null;

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
        $this->fixedLangKey = $lang;
        return $this;
    }

    final public function hasFixedLangKey(): bool
    {
        return $this->getFixedLangKey() !== '';
    }

    final public function getFixedLangKey(): string
    {
        return trim($this->fixedLangKey);
    }
}