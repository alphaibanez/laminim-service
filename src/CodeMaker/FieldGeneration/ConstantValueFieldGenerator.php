<?php

namespace Lkt\CodeMaker\FieldGeneration;

use Lkt\CodeMaker\Interfaces\FieldGenerator;
use Lkt\CodeMaker\Traits\FieldGeneratorCommon;

class ConstantValueFieldGenerator implements FieldGenerator
{
    use FieldGeneratorCommon;

    public function getGetters(): string
    {
        $r = [];

        $r[] = "public function get{$this->data->methodName}():{$this->data->getterReturnType} { return \$this->constantData->get('{$this->data->fieldName}'); }";

        return implode(' ', $r);
    }

    public function getSetters(): string
    {
        return '';
    }

    public function getCheckers(): string
    {
        return '';
    }

    public function parse(): string
    {
        return $this->getGetters();
    }
}