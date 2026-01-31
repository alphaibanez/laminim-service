<?php

namespace Lkt\Factory\Schemas\Traits;

trait DateFieldWithFormattedValueTrait
{
    protected $defaultReadFormat = '';
    protected array $langDefaultReadFormat = [];

    public function setDefaultReadFormat(string|callable $format): static
    {
        $this->defaultReadFormat = $format;
        return $this;
    }

    public function getDefaultReadFormat(): string
    {
        if (is_callable($this->defaultReadFormat)) {
            return call_user_func($this->defaultReadFormat);
        }
        return $this->defaultReadFormat;
    }

    public function setLangDefaultReadFormat(string|callable $format, string $lang): static
    {
        $this->langDefaultReadFormat[$lang] = $format;
        return $this;
    }

    public function getLangDefaultReadFormat(string $lang): ?string
    {
        if (!isset($this->langDefaultReadFormat[$lang])) return null;
        if (is_callable($this->langDefaultReadFormat[$lang])) {
            return call_user_func($this->langDefaultReadFormat[$lang]);
        }
        return $this->langDefaultReadFormat[$lang];
    }
}