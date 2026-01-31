<?php

namespace Lkt\Factory\Schemas\Fields;

use Lkt\Factory\Schemas\Traits\FieldWithJsonI18nStorageTrait;
use Lkt\Factory\Schemas\Traits\FieldWithMandatoryOptionTrait;
use Lkt\Factory\Schemas\Traits\FieldWithNullOptionTrait;

class StringField extends AbstractField
{
    const TYPE = 'text';

    protected bool $unique = false;

    use FieldWithNullOptionTrait,
        FieldWithJsonI18nStorageTrait,
        FieldWithMandatoryOptionTrait;

    public function setIsUnique(bool $isUnique = true): static
    {
        $this->unique = $isUnique;
        return $this;
    }

    public function isUnique(): bool
    {
        return $this->unique;
    }
}