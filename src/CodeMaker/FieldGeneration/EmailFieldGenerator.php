<?php

namespace Lkt\CodeMaker\FieldGeneration;

use Lkt\Attributes\LaminimUse;
use Lkt\CodeMaker\Interfaces\FieldGenerator;
use Lkt\CodeMaker\Traits\FieldGeneratorCommon;
use Lkt\Factory\Fields\Interfaces\Field;
use Lkt\Factory\Instance\Traits\ItemWithStringDataTrait;

#[LaminimUse]
class EmailFieldGenerator implements FieldGenerator
{
    use FieldGeneratorCommon;

    public function getGetters(): string
    {
        $r = [];

        $r[] = "public function get{$this->data->methodName}():string|null { return \$this->stringData->get('{$this->data->fieldName}'); }";

        return implode(' ', $r);
    }
    
    public function getSetters(): string
    {
        $r = [];

        $r[] = "/** @return {$this->data->selfReturningAnnotation} */";
        $r[] = "public function set{$this->data->methodName}(string \${$this->data->fieldName}):static { \$this->stringData->set('{$this->data->fieldName}', \${$this->data->fieldName}); return \$this; }";

        return implode(' ', $r);
    }

    public function getCheckers(): string
    {
        $r = [];

        $r[] = "public function has{$this->data->methodName}():bool { return \$this->stringData->has('{$this->data->fieldName}'); }";

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

    public static function generateTraitsUsageCode(Field $field): array
    {
        return [
            ItemWithStringDataTrait::class
        ];
    }
}