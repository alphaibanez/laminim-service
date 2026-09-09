<?php

namespace Lkt\Factory\Schemas\Fields;

use Lkt\Factory\Fields\Traits\FieldWithAppendForeignKeysNameOptionTrait;
use Lkt\Factory\Fields\Traits\FieldWithComponentOptionTrait;
use Lkt\Factory\Fields\Traits\FieldWithNullOptionTrait;
use Lkt\Factory\Fields\Traits\FieldWithOrderOptionTrait;
use Lkt\Factory\Fields\Traits\FieldWithRelatedAccessPolicyOptionTrait;
use Lkt\Factory\Fields\Traits\FieldWithRelatedClonePolicyOptionTrait;
use Lkt\Factory\Fields\Traits\FieldWithSoftTypedOptionTrait;
use Lkt\Factory\Fields\Traits\FieldWithWhereOptionTrait;

class RelatedKeysField extends AbstractField
{
    use FieldWithComponentOptionTrait,
        FieldWithWhereOptionTrait,
        FieldWithOrderOptionTrait,
        FieldWithSoftTypedOptionTrait,
        FieldWithRelatedAccessPolicyOptionTrait,
        FieldWithAppendForeignKeysNameOptionTrait,
        FieldWithRelatedClonePolicyOptionTrait,
        FieldWithNullOptionTrait;

    public static function defineRelation(string $component, string $name, string $column = ''): static
    {
        return (new static($name, $column))->setComponent($component);
    }
}