<?php

namespace Lkt\Factory\Schemas\Fields;

use Lkt\Factory\Schemas\Traits\FieldWithAvailableOptionsFilterOptionTrait;
use Lkt\Factory\Schemas\Traits\FieldWithComponentOptionTrait;
use Lkt\Factory\Schemas\Traits\FieldWithCompositionOptionTrait;
use Lkt\Factory\Schemas\Traits\FieldWithDynamicComponentOptionTrait;
use Lkt\Factory\Schemas\Traits\FieldWithRelatedAccessPolicyOptionTrait;
use Lkt\Factory\Schemas\Traits\FieldWithSoftTypedOptionTrait;
use Lkt\Factory\Schemas\Traits\FieldWithWhereOptionTrait;

class ForeignKeyField extends IntegerField
{
    use FieldWithComponentOptionTrait,
        FieldWithDynamicComponentOptionTrait,
        FieldWithWhereOptionTrait,
        FieldWithAvailableOptionsFilterOptionTrait,
        FieldWithSoftTypedOptionTrait,
        FieldWithCompositionOptionTrait,
        FieldWithRelatedAccessPolicyOptionTrait;

    public static function defineRelation(string $component, string $name, string $column = ''): static
    {
        return (new static($name, $column))->setComponent($component);
    }

    public function keyIsId(string $key): bool
    {
        return $key === $this->getName() . 'Id';
    }

    protected bool $onReadIncludeOptions = false;

    public function setOnReadIncludeOptions(bool $value = true): static
    {
        $this->onReadIncludeOptions = $value;
        return $this;
    }

    public function hasOnReadIncludeOptions(): bool
    {
        return $this->onReadIncludeOptions;
    }
}