<?php

namespace Lkt\CodeMaker\FieldGeneration;

use Lkt\CodeMaker\Interfaces\FieldGenerator;
use Lkt\CodeMaker\Traits\FieldGeneratorCommon;
use Lkt\Factory\Instance\Traits\ItemWithFloatDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithIntegerDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithMultipleFloatDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithMultipleIntegerDataTrait;
use Lkt\Factory\Schemas\Fields\AbstractField;
use Lkt\Factory\Schemas\Fields\FloatField;
use Lkt\Factory\Schemas\Fields\IntegerField;

class IntegerFieldGenerator implements FieldGenerator
{
    use FieldGeneratorCommon;

    public function getGetters(): string
    {
        $r = [];

        if ($this->data->isMultiple) {
            $r[] = "/** @return int[] */";
            $r[] = "public function get{$this->data->methodName}():array|null { return \$this->multipleIntegerData->get('{$this->data->fieldName}'); }";

        } else {
            $r[] = "public function get{$this->data->methodName}():int|null { return \$this->integerData->get('{$this->data->fieldName}'); }";
        }


        return implode(' ', $r);
    }
    
    public function getSetters(): string
    {
        $r = [];

        $r[] = "/** @return {$this->data->selfReturningAnnotation} */";
        if ($this->data->isMultiple) {
            $r[] = "public function set{$this->data->methodName}(array \${$this->data->fieldName}):static { \$this->multipleIntegerData->set('{$this->data->fieldName}', \${$this->data->fieldName}); return \$this; }";

        } else {
            $enumClass = $this->getEnumChoiceClass();
            $r[] = "public function set{$this->data->methodName}(int{$enumClass} \${$this->data->fieldName}):static { \$this->integerData->set('{$this->data->fieldName}', \${$this->data->fieldName}); return \$this; }";
        }

        return implode(' ', $r);
    }

    public function getCheckers(): string
    {
        $r = [];

        $lowerFieldMethod = lcfirst($this->data->methodName);

        if ($this->data->isMultiple) {
            $r[] = "public function {$lowerFieldMethod}Is(array \$value):bool { return \$this->multipleIntegerData->equal('{$this->data->fieldName}', \$value); }";
            $r[] = "public function has{$this->data->methodName}():bool { return \$this->multipleIntegerData->has('{$this->data->fieldName}'); }";
            $r[] = "public function has{$this->data->methodName}In(array \$values):bool { return \$this->multipleIntegerData->in('{$this->data->fieldName}', \$values); }";

        } else {
            $enumClass = $this->getEnumChoiceClass();
            $r[] = "public function {$lowerFieldMethod}Is(int{$enumClass} \$value):bool { return \$this->integerData->equal('{$this->data->fieldName}', \$value); }";
            $r[] = "public function has{$this->data->methodName}():bool { return \$this->integerData->has('{$this->data->fieldName}'); }";
            $r[] = "public function has{$this->data->methodName}In(array \$values):bool { return \$this->integerData->in('{$this->data->fieldName}', \$values); }";
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

    public static function generateTraitsUsageCode(AbstractField $field): array
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