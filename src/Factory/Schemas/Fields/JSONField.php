<?php

namespace Lkt\Factory\Schemas\Fields;

use Lkt\Factory\Schemas\Traits\FieldWithCompressOptionTrait;
use Lkt\Factory\Schemas\Traits\FieldWithEmptyDataModeTrait;
use Lkt\Factory\Schemas\Traits\FieldWithNullOptionTrait;

class JSONField extends AbstractField
{
    use FieldWithCompressOptionTrait,
        FieldWithNullOptionTrait,
        FieldWithEmptyDataModeTrait;

    protected bool $assoc = false;

    /**
     * @deprecated use associative constructor insted
     * @param bool $assoc
     * @return $this
     */
    public function setIsAssoc(bool $assoc = true): static
    {
        $this->assoc = $assoc;
        return $this;
    }

    public function isAssoc(bool $assoc = true): bool
    {
        return $this->assoc;
    }

    protected bool $storeAsI18nJson = false;

    final public function setIsI18nJson(bool $allow = true): self
    {
        $this->storeAsI18nJson = $allow;
        return $this;
    }

    final public function isI18nJson(): bool
    {
        return $this->storeAsI18nJson;
    }

    public static function associative(string $name, string $column = ''): static
    {
        $ins = static::define($name, $column);
        $ins->assoc = true;
        return $ins;
    }
}