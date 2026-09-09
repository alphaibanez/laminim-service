<?php

namespace Lkt\Factory\Schemas\Fields;

use Lkt\Factory\Fields\Traits\FieldWithAllowAnonymousOptionTrait;
use Lkt\Factory\Fields\Traits\FieldWithComponentOptionTrait;
use Lkt\Factory\Fields\Traits\FieldWithDynamicComponentOptionTrait;
use Lkt\Factory\Fields\Traits\FieldWithNullOptionTrait;
use Lkt\Factory\Fields\Traits\FieldWithOrderOptionTrait;
use Lkt\Factory\Fields\Traits\FieldWithPrefabRoleTrait;
use Lkt\Factory\Fields\Traits\FieldWithRelatedAccessPolicyOptionTrait;
use Lkt\Factory\Fields\Traits\FieldWithRelatedClonePolicyOptionTrait;
use Lkt\Factory\Fields\Traits\FieldWithRelatedComponentFeedsTrait;
use Lkt\Factory\Fields\Traits\FieldWithSoftTypedOptionTrait;
use Lkt\Factory\Fields\Traits\FieldWithWhereOptionTrait;

class ForeignKeysField extends AbstractField
{
    use FieldWithComponentOptionTrait,
        FieldWithDynamicComponentOptionTrait,
        FieldWithWhereOptionTrait,
        FieldWithOrderOptionTrait,
        FieldWithSoftTypedOptionTrait,
        FieldWithAllowAnonymousOptionTrait,
        FieldWithNullOptionTrait,
        FieldWithRelatedComponentFeedsTrait,
        FieldWithRelatedAccessPolicyOptionTrait,
        FieldWithPrefabRoleTrait,
        FieldWithRelatedClonePolicyOptionTrait;

    public static function defineRelation(string $component, string $name, string $column = ''): static
    {
        return (new static($name, $column))->setComponent($component);
    }

    public function keyIsIds(string $key): bool
    {
        return $key === $this->getName() . 'Ids';
    }
}