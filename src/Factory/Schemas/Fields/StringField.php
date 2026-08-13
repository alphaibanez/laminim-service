<?php

namespace Lkt\Factory\Schemas\Fields;

use Lkt\Factory\Instance\Enums\TrimMode;
use Lkt\Factory\Schemas\Traits\FieldWithEmptyDataModeTrait;
use Lkt\Factory\Schemas\Traits\FieldWithInvalidDataModeTrait;
use Lkt\Factory\Schemas\Traits\FieldWithJsonI18nStorageTrait;
use Lkt\Factory\Schemas\Traits\FieldWithMandatoryOptionTrait;
use Lkt\Factory\Schemas\Traits\FieldWithNullOptionTrait;

class StringField extends AbstractField
{
    protected bool $unique = false;
    protected TrimMode $trimMode = TrimMode::Full;

    use FieldWithNullOptionTrait,
        FieldWithJsonI18nStorageTrait,
        FieldWithMandatoryOptionTrait,
        FieldWithInvalidDataModeTrait,
        FieldWithEmptyDataModeTrait;

    public function setIsUnique(bool $isUnique = true): static
    {
        $this->unique = $isUnique;
        return $this;
    }

    public function isUnique(): bool
    {
        return $this->unique;
    }

    public function getTrimMode(): TrimMode
    {
        return $this->trimMode;
    }

    public function setTrimMode(TrimMode $mode): static
    {
        $this->trimMode = $mode;
        return $this;
    }
}