<?php

namespace Lkt\CodeMaker\FieldGeneration;

use Lkt\CodeMaker\Interfaces\FieldGenerator;
use Lkt\CodeMaker\Traits\FieldGeneratorCommon;

class BooleanFieldGenerator implements FieldGenerator
{
    use FieldGeneratorCommon;

    public function getGetters(): string
    {
        $r = [];

        $r[] = "public function {$this->data->fieldName}():bool { return \$this->booleanData->get('{$this->data->fieldName}'); }";

        return implode(' ', $r);
    }

    public function getSetters(): string
    {
        $r = [];

        $r[] = "/** @return {$this->data->selfReturningAnnotation} */";
        $r[] = "public function set{$this->data->methodName}(bool \${$this->data->fieldName}):static { \$this->booleanData->set('{$this->data->fieldName}', \${$this->data->fieldName}); return \$this; }";

        return implode(' ', $r);
    }

    public function getCheckers(): string
    {
        return '';
    }

    public function parse(): string
    {
        return implode(' ', [
            $this->getGetters(),
            $this->getSetters(),
        ]);
    }
}