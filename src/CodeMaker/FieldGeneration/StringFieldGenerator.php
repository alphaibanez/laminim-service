<?php

namespace Lkt\CodeMaker\FieldGeneration;

use Lkt\CodeMaker\Interfaces\FieldGenerator;
use Lkt\CodeMaker\Traits\FieldGeneratorCommon;
use Lkt\Factory\Schemas\Fields\ValueListField;

class StringFieldGenerator implements FieldGenerator
{
    use FieldGeneratorCommon;

    public function getGetters(): string
    {
        $r = [];

        if ($this->data->isMultiple) {
            $r[] = "/** @return string[] */";

            if ($this->data->field instanceof ValueListField) {
                $r[] = "public function get{$this->data->methodName}():string|null { return \$this->multipleStringData->get('{$this->data->fieldName}'); }";
                $r[] = "public function get{$this->data->methodName}AsArray():array|null { return \$this->multipleStringData->get('{$this->data->fieldName}'); }";

            } else {
                $r[] = "public function get{$this->data->methodName}():array|null { return \$this->multipleStringData->get('{$this->data->fieldName}'); }";
            }



        } else {
            $r[] = "public function get{$this->data->methodName}():string|null { return \$this->stringData->get('{$this->data->fieldName}'); }";
        }


        return implode(' ', $r);
    }
    
    public function getSetters(): string
    {
        $r = [];

        $r[] = "/** @return {$this->data->selfReturningAnnotation} */";
        if ($this->data->isMultiple) {
            $r[] = "public function set{$this->data->methodName}(array \${$this->data->fieldName}):static { \$this->multipleStringData->set('{$this->data->fieldName}', \${$this->data->fieldName}); return \$this; }";

        } else {
            $enumClass = $this->getEnumChoiceClass();
            $r[] = "public function set{$this->data->methodName}(string{$enumClass} \${$this->data->fieldName}):static { \$this->stringData->set('{$this->data->fieldName}', \${$this->data->fieldName}); return \$this; }";
        }

        return implode(' ', $r);
    }

    public function getCheckers(): string
    {
        $r = [];

        $lowerFieldMethod = lcfirst($this->data->methodName);

        if ($this->data->isMultiple) {
            $r[] = "public function {$lowerFieldMethod}Is(array \$value):bool { return \$this->multipleStringData->equal('{$this->data->fieldName}', \$value); }";
            $r[] = "public function has{$this->data->methodName}():bool { return \$this->multipleStringData->has('{$this->data->fieldName}'); }";
            $r[] = "public function has{$this->data->methodName}In(array \$values):bool { return \$this->multipleStringData->in('{$this->data->fieldName}', \$values); }";

        } else {
            $enumClass = $this->getEnumChoiceClass();
            $r[] = "public function {$lowerFieldMethod}Is(string{$enumClass} \$value):bool { return \$this->stringData->equal('{$this->data->fieldName}', \$value); }";
            $r[] = "public function has{$this->data->methodName}():bool { return \$this->stringData->has('{$this->data->fieldName}'); }";
            $r[] = "public function has{$this->data->methodName}In(array \$values):bool { return \$this->stringData->in('{$this->data->fieldName}', \$values); }";
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
}