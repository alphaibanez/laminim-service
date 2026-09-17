<?php

namespace Lkt\Factory\Schemas\Fields;

use Lkt\Factory\Fields\Enums\IntegerFieldType;
use Lkt\Factory\Fields\Traits\FieldWithAvailableOptionsFilterOptionTrait;
use Lkt\Factory\Fields\Traits\FieldWithChoiceOptionTrait;
use Lkt\Factory\Fields\Traits\FieldWithComponentOptionTrait;
use Lkt\Factory\Fields\Traits\FieldWithCompositionOptionTrait;
use Lkt\Factory\Fields\Traits\FieldWithDynamicComponentOptionTrait;
use Lkt\Factory\Fields\Traits\FieldWithEmptyDataModeTrait;
use Lkt\Factory\Fields\Traits\FieldWithInvalidDataModeTrait;
use Lkt\Factory\Fields\Traits\FieldWithMultipleOptionTrait;
use Lkt\Factory\Fields\Traits\FieldWithNullOptionTrait;
use Lkt\Factory\Fields\Traits\FieldWithOnReadIncludeOptionsTrait;
use Lkt\Factory\Fields\Traits\FieldWithPrefabRoleTrait;
use Lkt\Factory\Fields\Traits\FieldWithRelatedAccessPolicyOptionTrait;
use Lkt\Factory\Fields\Traits\FieldWithRelatedClonePolicyOptionTrait;
use Lkt\Factory\Fields\Traits\FieldWithSoftTypedOptionTrait;
use Lkt\Factory\Fields\Traits\FieldWithWhereOptionTrait;

class IntegerField extends AbstractField
{
    use FieldWithNullOptionTrait,
        FieldWithMultipleOptionTrait,
        FieldWithInvalidDataModeTrait,
        FieldWithEmptyDataModeTrait,
        FieldWithChoiceOptionTrait,
        FieldWithPrefabRoleTrait,

        FieldWithComponentOptionTrait,
        FieldWithDynamicComponentOptionTrait,
        FieldWithWhereOptionTrait,
        FieldWithAvailableOptionsFilterOptionTrait,
        FieldWithSoftTypedOptionTrait,
        FieldWithCompositionOptionTrait,
        FieldWithRelatedAccessPolicyOptionTrait,
        FieldWithOnReadIncludeOptionsTrait,
        FieldWithRelatedClonePolicyOptionTrait;

    protected IntegerFieldType $fieldType = IntegerFieldType::Integer;

    protected int|null $minValue = null;

    public function setMinValue(int $val): static
    {
        $this->minValue = $val;
        return $this;
    }

    public function getMinValue(): int|null
    {
        return $this->minValue;
    }

    public static function foreignKey(string $component, string $name, string $column = ''): static
    {
        $ins = new static($name, $column);
        $ins->component = $component;
        $ins->fieldType = IntegerFieldType::ForeignKey;
        return $ins;
    }

    public static function dynamicForeignKey(string $dynamicComponentField, string $name, string $column = ''): static
    {
        $ins = new static($name, $column);
        $ins->dynamicComponentField = $dynamicComponentField;
        $ins->fieldType = IntegerFieldType::ForeignKey;
        return $ins;
    }

    public static function leftPivot(string $component, string $name, string $column = ''): static
    {
        $ins = new static($name, $column);
        $ins->component = $component;
        $ins->fieldType = IntegerFieldType::LeftPivot;
        $ins->isIdentifier = true;
        return $ins;
    }

    public static function dynamicLeftPivot(string $dynamicComponentField, string $name, string $column = ''): static
    {
        $ins = new static($name, $column);
        $ins->dynamicComponentField = $dynamicComponentField;
        $ins->fieldType = IntegerFieldType::LeftPivot;
        $ins->isIdentifier = true;
        return $ins;
    }

    public static function rightPivot(string $component, string $name, string $column = ''): static
    {
        $ins = new static($name, $column);
        $ins->component = $component;
        $ins->fieldType = IntegerFieldType::RightPivot;
        $ins->isIdentifier = true;
        return $ins;
    }

    public static function dynamicRightPivot(string $dynamicComponentField, string $name, string $column = ''): static
    {
        $ins = new static($name, $column);
        $ins->dynamicComponentField = $dynamicComponentField;
        $ins->fieldType = IntegerFieldType::RightPivot;
        $ins->isIdentifier = true;
        return $ins;
    }

    public static function position(string $component, string $name, string $column = ''): static
    {
        $ins = new static($name, $column);
        $ins->component = $component;
        $ins->fieldType = IntegerFieldType::Position;
        return $ins;
    }

    public function keyIsId(string $key): bool
    {
        return $key === $this->getName() . 'Id';
    }

    public function isForeignKey(): bool
    {
        return $this->fieldType === IntegerFieldType::ForeignKey;
    }

    public function isLeftPivot(): bool
    {
        return $this->fieldType === IntegerFieldType::LeftPivot;
    }

    public function isRightPivot(): bool
    {
        return $this->fieldType === IntegerFieldType::RightPivot;
    }
}