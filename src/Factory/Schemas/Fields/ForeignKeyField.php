<?php

namespace Lkt\Factory\Schemas\Fields;

use Lkt\Factory\Schemas\Traits\FieldWithAvailableOptionsFilterOptionTrait;
use Lkt\Factory\Schemas\Traits\FieldWithComponentOptionTrait;
use Lkt\Factory\Schemas\Traits\FieldWithCompositionOptionTrait;
use Lkt\Factory\Schemas\Traits\FieldWithDynamicComponentOptionTrait;
use Lkt\Factory\Schemas\Traits\FieldWithOnReadIncludeOptionsTrait;
use Lkt\Factory\Schemas\Traits\FieldWithPrefabRoleTrait;
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
        FieldWithRelatedAccessPolicyOptionTrait,
        FieldWithPrefabRoleTrait,
        FieldWithOnReadIncludeOptionsTrait;

    public static function defineRelation(string $component, string $name, string $column = ''): static
    {
        return (new static($name, $column))->setComponent($component);
    }

    public function keyIsId(string $key): bool
    {
        return $key === $this->getName() . 'Id';
    }
}