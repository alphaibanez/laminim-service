<?php

namespace Lkt\CodeMaker\FieldGeneration;

use Lkt\CodeMaker\Interfaces\FieldGenerator;
use Lkt\CodeMaker\Traits\FieldGeneratorCommon;
use Lkt\Factory\Instance\Traits\ItemWithFloatDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithMultipleFloatDataTrait;
use Lkt\Factory\Schemas\Fields\AbstractField;
use Lkt\Factory\Schemas\Fields\FloatField;

class FloatFieldGenerator implements FieldGenerator
{
    use FieldGeneratorCommon;

    public function getGetters(): string
    {
        $r = [];

        if ($this->data->isMultiple) {
            $r[] = "/** @return float[]|null */";
            $r[] = "public function get{$this->data->methodName}():array|null { return \$this->multipleFloatData->get('{$this->data->fieldName}'); }";

        } else {
            $r[] = "public function get{$this->data->methodName}():float|null { return \$this->floatData->get('{$this->data->fieldName}'); }";
            $r[] = "public function get{$this->data->methodName}Formatted():string|null { return \$this->floatData->formatted('{$this->data->fieldName}'); }";
        }

        return implode(' ', $r);
    }
    
    public function getSetters(): string
    {
        $r = [];

        $r[] = "/** @return {$this->data->selfReturningAnnotation} */";
        if ($this->data->isMultiple) {
            $r[] = "public function set{$this->data->methodName}(array \${$this->data->fieldName}):static { \$this->multipleFloatData->set('{$this->data->fieldName}', \${$this->data->fieldName}); return \$this; }";

        } else {
            $r[] = "public function set{$this->data->methodName}(float \${$this->data->fieldName}):static { \$this->floatData->set('{$this->data->fieldName}', \${$this->data->fieldName}); return \$this; }";
        }

        return implode(' ', $r);
    }

    public function getCheckers(): string
    {
        $r = [];

        if ($this->data->isMultiple) {
            $r[] = "public function has{$this->data->methodName}():bool { return \$this->multipleFloatData->has('{$this->data->fieldName}'); }";
        } else {
            $r[] = "public function has{$this->data->methodName}():bool { return \$this->floatData->has('{$this->data->fieldName}'); }";
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
        if ($field instanceof FloatField && $field->isMultiple()) {
            return [
                ItemWithMultipleFloatDataTrait::class
            ];
        }
        return [
            ItemWithFloatDataTrait::class
        ];
    }
}