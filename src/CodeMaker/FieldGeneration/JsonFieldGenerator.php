<?php

namespace Lkt\CodeMaker\FieldGeneration;

use Lkt\CodeMaker\Interfaces\FieldGenerator;
use Lkt\CodeMaker\Traits\FieldGeneratorCommon;
use Lkt\Factory\Schemas\Fields\EncryptField;
use Lkt\Factory\Schemas\Fields\JSONField;

class JsonFieldGenerator implements FieldGenerator
{
    use FieldGeneratorCommon;

    public function getGetters(): string
    {
        $r = [];

        if ($this->data->field instanceof JSONField) {
            if ($this->data->field->isAssoc()) {
                $r[] = "public function get{$this->data->methodName}():array|null { return \$this->jsonData->get('{$this->data->fieldName}'); }";

            } else {
                $r[] = "public function get{$this->data->methodName}():\StdClass|null { return \$this->jsonData->get('{$this->data->fieldName}'); }";
            }
        }

        return implode(' ', $r);
    }
    
    public function getSetters(): string
    {
        $r = [];

        $r[] = "/** @return {$this->data->selfReturningAnnotation} */";


        if ($this->data->field instanceof JSONField) {
            if ($this->data->field->isAssoc()) {
                $r[] = "public function set{$this->data->methodName}(array \${$this->data->fieldName}):static { return \$this->jsonData->set('{$this->data->fieldName}', \${$this->data->fieldName}); }";

            } else {
                $r[] = "public function set{$this->data->methodName}(\StdClass \${$this->data->fieldName}):static { return \$this->jsonData->set('{$this->data->fieldName}', \${$this->data->fieldName}); }";
            }
        }

        return implode(' ', $r);
    }

    public function getCheckers(): string
    {
        $r = [];
        $r[] = "public function has{$this->data->methodName}():bool { return \$this->jsonData->has('{$this->data->fieldName}'); }";

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