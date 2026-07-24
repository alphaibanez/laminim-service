<?php

namespace Lkt\CodeMaker\FieldGeneration;

use Lkt\CodeMaker\Interfaces\FieldGenerator;
use Lkt\CodeMaker\Traits\FieldGeneratorCommon;

class ConcatFieldGenerator implements FieldGenerator
{
    use FieldGeneratorCommon;

    public function getGetters(): string
    {
        $r = [];

        $r[] = "public function get{$this->data->methodName}():string { return \$this->concatData->get('{$this->data->fieldName}'); }";

        return implode(' ', $r);
    }

    public function getSetters(): string
    {
        return '';
    }

    public function getCheckers(): string
    {
        return "public function has{$this->data->methodName}():string { return \$this->concatData->has('{$this->data->fieldName}'); }";
    }

    public function parse(): string
    {
        return implode(' ', [
            $this->getGetters(),
            $this->getCheckers(),
        ]);
    }
}