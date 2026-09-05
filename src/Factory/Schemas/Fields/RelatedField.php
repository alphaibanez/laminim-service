<?php

namespace Lkt\Factory\Schemas\Fields;

use Lkt\Factory\Schemas\Traits\FieldWithComponentOptionTrait;
use Lkt\Factory\Schemas\Traits\FieldWithCompositionOptionTrait;
use Lkt\Factory\Schemas\Traits\FieldWithMultipleReferencesTrait;
use Lkt\Factory\Schemas\Traits\FieldWithOrderOptionTrait;
use Lkt\Factory\Schemas\Traits\FieldWithPaginationOptionTrait;
use Lkt\Factory\Schemas\Traits\FieldWithRelatedAccessPolicyOptionTrait;
use Lkt\Factory\Schemas\Traits\FieldWithRelatedComponentFeedsTrait;
use Lkt\Factory\Schemas\Traits\FieldWithSingleModeOptionTrait;
use Lkt\Factory\Schemas\Traits\FieldWithSoftTypedOptionTrait;
use Lkt\Factory\Schemas\Traits\FieldWithWhereOptionTrait;

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
        FieldWithRelatedAccessPolicyOptionTrait;

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