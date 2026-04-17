<?php

namespace Lkt\CodeMaker\FieldGeneration;

class FloatFieldGenerator extends AbstractFieldGenerator
{
    public function getGetters(): string
    {
        $r = [];

        if ($this->data->isMultiple) {
            $r[] = "/** @return float[] */";
            $r[] = "public function get{$this->data->methodName}():array { return \$this->_getFloatVal('{$this->data->fieldName}'); }";

        } else {
            $r[] = "public function get{$this->data->methodName}():float { return \$this->_getFloatVal('{$this->data->fieldName}'); }";
            $r[] = "public function get{$this->data->methodName}Formatted():float { return \$this->_getFloatFormattedVal('{$this->data->fieldName}'); }";
        }

        return implode(' ', $r);
    }
    
    public function getSetters(): string
    {
        $r = [];

        $r[] = "/** @return {$this->data->selfReturningAnnotation} */";
        if ($this->data->isMultiple) {
            $r[] = "public function set{$this->data->methodName}(array \${$this->data->fieldName}):static { return \$this->_setFloatVal('{$this->data->fieldName}', \${$this->data->fieldName}); }";

        } else {
            $r[] = "public function set{$this->data->methodName}(float \${$this->data->fieldName}):static { return \$this->_setFloatVal('{$this->data->fieldName}', \${$this->data->fieldName}); }";
        }

        return implode(' ', $r);
    }

    public function getCheckers(): string
    {
        $r = [];

        $r[] = "public function has{$this->data->methodName}():bool { return \$this->_hasFloatVal('{$this->data->fieldName}'); }";

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