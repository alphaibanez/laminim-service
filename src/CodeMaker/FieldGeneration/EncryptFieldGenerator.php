<?php

namespace Lkt\CodeMaker\FieldGeneration;

use Lkt\CodeMaker\Interfaces\FieldGenerator;
use Lkt\CodeMaker\Traits\FieldGeneratorCommon;
use Lkt\Factory\Schemas\Fields\EncryptField;
use Lkt\Factory\Schemas\Fields\ValueListField;

class EncryptFieldGenerator implements FieldGenerator
{
    use FieldGeneratorCommon;

    public function getGetters(): string
    {
        $r = [];

        if ($this->data->isMultiple) {
            $r[] = "/** @return string[] */";
//            $r[] = "public function get{$this->data->methodName}():array|null { return \$this->multipleEncryptData->get('{$this->data->fieldName}'); }";

        } else {
            $r[] = "public function get{$this->data->methodName}():string|null { return \$this->encryptData->get('{$this->data->fieldName}'); }";

            if ($this->data->field instanceof EncryptField && $this->data->field->isHashMode()) {
                $r[] = "public function get{$this->data->methodName}Decrypted():string|null { return \$this->encryptData->decrypt('{$this->data->fieldName}'); }";
            }
        }


        return implode(' ', $r);
    }
    
    public function getSetters(): string
    {
        $r = [];

        $r[] = "/** @return {$this->data->selfReturningAnnotation} */";
        if ($this->data->isMultiple) {
//            $r[] = "public function set{$this->data->methodName}(array \${$this->data->fieldName}):static { \$this->multipleStringData->set('{$this->data->fieldName}', \${$this->data->fieldName}); return \$this; }";

        } else {
            $enumClass = $this->getEnumChoiceClass();
            $r[] = "public function set{$this->data->methodName}(string{$enumClass} \${$this->data->fieldName}):static { \$this->encryptData->set('{$this->data->fieldName}', \${$this->data->fieldName}); return \$this; }";
        }

        return implode(' ', $r);
    }

    public function getCheckers(): string
    {
        $r = [];

        $lowerFieldMethod = lcfirst($this->data->methodName);

        if ($this->data->isMultiple) {
//            $r[] = "public function {$lowerFieldMethod}Is(array \$value):bool { return \$this->multipleStringData->equal('{$this->data->fieldName}', \$value); }";
//            $r[] = "public function has{$this->data->methodName}():bool { return \$this->multipleStringData->has('{$this->data->fieldName}'); }";
//            $r[] = "public function has{$this->data->methodName}In(array \$values):bool { return \$this->multipleStringData->in('{$this->data->fieldName}', \$values); }";

        } else {
            $r[] = "public function has{$this->data->methodName}():bool { return \$this->encryptData->has('{$this->data->fieldName}'); }";
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