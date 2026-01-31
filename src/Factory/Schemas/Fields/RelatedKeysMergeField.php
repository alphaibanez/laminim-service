<?php

namespace Lkt\Factory\Schemas\Fields;

use Lkt\Factory\Schemas\Exceptions\InvalidComponentException;
use Lkt\Factory\Schemas\Relations\RelatedRelation;
use Lkt\Factory\Schemas\Traits\FieldWithMultipleReferencesTrait;
use Lkt\Factory\Schemas\Traits\FieldWithOrderOptionTrait;
use Lkt\Factory\Schemas\Traits\FieldWithPaginationOptionTrait;
use Lkt\Factory\Schemas\Traits\FieldWithSingleModeOptionTrait;
use Lkt\Factory\Schemas\Traits\FieldWithSoftTypedOptionTrait;
use Lkt\Factory\Schemas\Traits\FieldWithWhereOptionTrait;

class RelatedKeysMergeField extends AbstractField
{
    use FieldWithWhereOptionTrait,
        FieldWithOrderOptionTrait,
        FieldWithSoftTypedOptionTrait,
        FieldWithSingleModeOptionTrait,
        FieldWithMultipleReferencesTrait,
        FieldWithPaginationOptionTrait;

    protected array $additionalColumns = [];

    final public static function defineMerge(string $name, array $additionalColumns = []): static
    {
        $r = new static($name);
        $r->additionalColumns = $additionalColumns;
        return $r;
    }

    protected array $relations = [];

    /**
     * @throws InvalidComponentException
     */
    public function addRelation(string $component, string $fieldPointingMe, array $additionalColumns = [], callable $queryConfigurator = null): static
    {
        $this->relations[] = new RelatedRelation($component, $fieldPointingMe, $additionalColumns, $queryConfigurator);
        return $this;
    }

    /**
     * @return RelatedRelation[]
     */
    public function getRelations(): array
    {
        return $this->relations;
    }

    public function getAdditionalColumns(): array
    {
        return $this->additionalColumns;
    }

    public function hasAdditionalColumns(): bool
    {
        return count($this->additionalColumns) > 0;
    }
}