<?php

namespace Lkt\Factory\Schemas\Fields;

use Lkt\Attributes\Deprecated;
use Lkt\Factory\Fields\Traits\FieldWithAppendForeignKeysNameOptionTrait;
use Lkt\Factory\Fields\Traits\FieldWithComponentOptionTrait;
use Lkt\Factory\Fields\Traits\FieldWithNullOptionTrait;
use Lkt\Factory\Fields\Traits\FieldWithOrderOptionTrait;
use Lkt\Factory\Fields\Traits\FieldWithRelatedAccessPolicyOptionTrait;
use Lkt\Factory\Fields\Traits\FieldWithRelatedClonePolicyOptionTrait;
use Lkt\Factory\Fields\Traits\FieldWithSoftTypedOptionTrait;
use Lkt\Factory\Fields\Traits\FieldWithWhereOptionTrait;

/**
 * @deprecated
 *
 * This field it's considered an anti-pattern
 * and it's usage should be avoid.
 *
 * Due to it's inherit nature, causes a negative impact on
 * database queries.
 *
 * Only kept due to backward compatibility on legacy projects,
 * but it will be removed in the future.
 * Considerer using PivotField instead
 * or adjust the database structure in order to match the app logic.
 *
 * PivotField it's the most direct replacement.
 */
#[Deprecated]
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