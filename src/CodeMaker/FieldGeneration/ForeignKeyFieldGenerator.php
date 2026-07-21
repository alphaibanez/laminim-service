<?php

namespace Lkt\CodeMaker\FieldGeneration;

use Lkt\CodeMaker\Interfaces\FieldGenerator;
use Lkt\CodeMaker\Traits\FieldGeneratorCommon;

class ForeignKeyFieldGenerator implements FieldGenerator
{
    use FieldGeneratorCommon;

    public function getGetters(): string
    {
        $r = [];

        $r[] = "{$this->getRelatedReturnAnnotationFormatted()}";
        $r[] = "public function {$this->data->fieldName}(){$this->getRelatedReturnTypeFormatted()} { return \$this->foreignKeyData->getItem('{$this->data->fieldName}'); }";
        $r[] = "public function {$this->data->fieldName}Id():int { return \$this->foreignKeyData->get('{$this->data->fieldName}'); }";

        return implode(' ', $r);
    }

    public function getSetters(): string
    {
        $r = [];

        $r[] = "/** @return {$this->data->selfReturningAnnotation} */";
        $r[] = "public function set{$this->data->methodName}(int \${$this->data->fieldName}):static { \$this->foreignKeyData->set('{$this->data->fieldName}', \${$this->data->fieldName}); return \$this; }";

        return implode(' ', $r);
    }

    public function getCheckers(): string
    {
        $r = [];

        $r[] = "public function has<?php echo {$this->data->fieldName};?>Id() :bool { return \$this->foreignKeyData->has('{$this->data->fieldName}'); }";
        $r[] = "public function has<?php echo {$this->data->fieldName};?>() :bool { return \$this->foreignKeyData->has('{$this->data->fieldName}'); }";

        return implode(' ', $r);
    }

    public function parse(): string
    {
        return implode(' ', [
            $this->getGetters(),
            $this->getSetters(),
        ]);
    }
}