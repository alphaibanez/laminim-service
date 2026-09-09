<?php

namespace Lkt\Factory\Schemas\Fields;

use Lkt\Factory\Fields\Traits\FieldWithAvailableOptionsFilterOptionTrait;
use Lkt\Factory\Fields\Traits\FieldWithComponentOptionTrait;
use Lkt\Factory\Fields\Traits\FieldWithCompositionOptionTrait;
use Lkt\Factory\Fields\Traits\FieldWithDynamicComponentOptionTrait;
use Lkt\Factory\Fields\Traits\FieldWithEmptyDataModeTrait;
use Lkt\Factory\Fields\Traits\FieldWithInvalidDataModeTrait;
use Lkt\Factory\Fields\Traits\FieldWithOnReadIncludeOptionsTrait;
use Lkt\Factory\Fields\Traits\FieldWithPrefabRoleTrait;
use Lkt\Factory\Fields\Traits\FieldWithRelatedAccessPolicyOptionTrait;
use Lkt\Factory\Fields\Traits\FieldWithRelatedClonePolicyOptionTrait;
use Lkt\Factory\Fields\Traits\FieldWithSoftTypedOptionTrait;
use Lkt\Factory\Fields\Traits\FieldWithWhereOptionTrait;

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
        FieldWithOnReadIncludeOptionsTrait,
        FieldWithRelatedClonePolicyOptionTrait,
        FieldWithInvalidDataModeTrait,
        FieldWithEmptyDataModeTrait;

    public static function defineRelation(string $component, string $name, string $column = ''): static
    {
        return (new static($name, $column))->setComponent($component);
    }

    public function keyIsId(string $key): bool
    {
        return $key === $this->getName() . 'Id';
    }
}