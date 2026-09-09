<?php

namespace Lkt\Factory\Schemas\Fields;

use Lkt\Factory\Fields\Traits\FieldWithComponentOptionTrait;
use Lkt\Factory\Fields\Traits\FieldWithCompositionOptionTrait;
use Lkt\Factory\Fields\Traits\FieldWithMultipleReferencesTrait;
use Lkt\Factory\Fields\Traits\FieldWithOnParentDrop;
use Lkt\Factory\Fields\Traits\FieldWithOrderOptionTrait;
use Lkt\Factory\Fields\Traits\FieldWithPaginationOptionTrait;
use Lkt\Factory\Fields\Traits\FieldWithRelatedAccessPolicyOptionTrait;
use Lkt\Factory\Fields\Traits\FieldWithRelatedComponentFeedsTrait;
use Lkt\Factory\Fields\Traits\FieldWithSingleModeOptionTrait;
use Lkt\Factory\Fields\Traits\FieldWithSoftTypedOptionTrait;
use Lkt\Factory\Fields\Traits\FieldWithWhereOptionTrait;

class RelatedField extends AbstractField
{
    use FieldWithComponentOptionTrait,
        FieldWithWhereOptionTrait,
        FieldWithOrderOptionTrait,
        FieldWithSoftTypedOptionTrait,
        FieldWithSingleModeOptionTrait,
        FieldWithMultipleReferencesTrait,
        FieldWithPaginationOptionTrait,
        FieldWithRelatedComponentFeedsTrait,
        FieldWithCompositionOptionTrait,
        FieldWithRelatedAccessPolicyOptionTrait,
        FieldWithOnParentDrop;

    public static function defineRelation(string $component, string $name, string $column = ''): static
    {
        return (new static($name, $column))->setComponent($component);
    }

    public static function single(string $component, string $name, string $column = ''): static
    {
        $ins = new static($name, $column);
        $ins->component = $component;
        $ins->singleMode = true;
        return $ins;
    }

    protected bool $returnsEmptyOneInSingleMode = false;

    public function setReturnsEmptyOneInSingleMode(bool $enable = true): static
    {
        $this->returnsEmptyOneInSingleMode = $enable;
        return $this;
    }

    public function hasToReturnsEmptyOneInSingleMode(): bool
    {
        return $this->returnsEmptyOneInSingleMode;
    }
}