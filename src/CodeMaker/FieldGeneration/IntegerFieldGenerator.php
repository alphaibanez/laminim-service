<?php

namespace Lkt\CodeMaker\FieldGeneration;

use Lkt\Attributes\LaminimUse;
use Lkt\CodeMaker\Interfaces\FieldGenerator;
use Lkt\CodeMaker\Traits\FieldGeneratorCommon;
use Lkt\Factory\Fields\Interfaces\Field;
use Lkt\Factory\Instance\Traits\ItemWithIntegerDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithMultipleIntegerDataTrait;
use Lkt\Factory\Schemas\Fields\IntegerField;

#[LaminimUse]
class IntegerFieldGenerator implements FieldGenerator
{
    use FieldGeneratorCommon;

    public function getGetters(): string
    {
        $r = [];

        if ($this->data->isMultiple) {
            $r[] = "/** @return int[] */";
            $r[] = "public function get{$this->data->methodName}():array|null { return \$this->integerMD->get('{$this->data->fieldName}'); }";

        } else {
            $r[] = "public function get{$this->data->methodName}():int|null { return \$this->integerD->get('{$this->data->fieldName}'); }";
        }


        return implode(' ', $r);
    }
    
    public function getSetters(): string
    {
        $r = [];

        $r[] = "/** @return {$this->data->selfReturningAnnotation} */";
        if ($this->data->isMultiple) {
            $r[] = "public function set{$this->data->methodName}(array \${$this->data->fieldName}):static { \$this->integerMD->set('{$this->data->fieldName}', \${$this->data->fieldName}); return \$this; }";

        } else {
            $enumClass = $this->getEnumChoiceClass();
            $r[] = "public function set{$this->data->methodName}(int{$enumClass} \${$this->data->fieldName}):static { \$this->integerD->set('{$this->data->fieldName}', \${$this->data->fieldName}); return \$this; }";
        }

        return implode(' ', $r);
    }

    public function getCheckers(): string
    {
        $r = [];

        $lowerFieldMethod = lcfirst($this->data->methodName);

        if ($this->data->isMultiple) {
            $r[] = "public function {$lowerFieldMethod}Is(array \$value):bool { return \$this->integerMD->equal('{$this->data->fieldName}', \$value); }";
            $r[] = "public function has{$this->data->methodName}():bool { return \$this->integerMD->has('{$this->data->fieldName}'); }";
            $r[] = "public function has{$this->data->methodName}In(array \$values):bool { return \$this->integerMD->in('{$this->data->fieldName}', \$values); }";

        } else {
            $enumClass = $this->getEnumChoiceClass();
            $r[] = "public function {$lowerFieldMethod}Is(int{$enumClass} \$value):bool { return \$this->integerD->equal('{$this->data->fieldName}', \$value); }";
            $r[] = "public function has{$this->data->methodName}():bool { return \$this->integerD->has('{$this->data->fieldName}'); }";
            $r[] = "public function has{$this->data->methodName}In(array \$values):bool { return \$this->integerD->in('{$this->data->fieldName}', \$values); }";
        }

        return implode(' ', $r);
    }

    public function parse(): string
    {
        return implode(' ', [
            $this->getGetters(),
            $this->getSetters(),
            $this->getCheckers(),
        ]);
    }

    public static function generateTraitsUsageCode(Field $field): array
    {
        if ($field instanceof IntegerField && $field->isMultiple()) {
            return [
                ItemWithMultipleIntegerDataTrait::class
            ];
        }
        return [
            ItemWithIntegerDataTrait::class
        ];
    }
}