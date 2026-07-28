<?php

namespace Lkt\CodeMaker\FieldGeneration;

use Lkt\CodeMaker\Interfaces\FieldGenerator;
use Lkt\CodeMaker\Traits\FieldGeneratorCommon;
use Lkt\Factory\Instance\Traits\ItemWithColorDataTrait;
use Lkt\Factory\Schemas\Fields\AbstractField;

class ColorFieldGenerator implements FieldGenerator
{
    use FieldGeneratorCommon;

    public function getGetters(): string
    {
        $r = [];

        $r[] = "public function get{$this->data->methodName}():string|null { return \$this->colorData->get('{$this->data->fieldName}'); }";
        $r[] = "public function get{$this->data->methodName}Rgb(float \$opacity = null):array|null { return \$this->colorData->getRGBA('{$this->data->fieldName}', \$opacity); }";
        $r[] = "public function get{$this->data->methodName}RgbFormatted(float \$opacity = null):array|null { return \$this->colorData->getRGBAString('{$this->data->fieldName}', \$opacity); }";

        return implode(' ', $r);
    }
    
    public function getSetters(): string
    {
        $r = [];

        $r[] = "/** @return {$this->data->selfReturningAnnotation} */";
        $r[] = "public function set{$this->data->methodName}(string \${$this->data->fieldName}):static { \$this->colorData->set('{$this->data->fieldName}', \${$this->data->fieldName}); return \$this; }";

        return implode(' ', $r);
    }

    public function getCheckers(): string
    {
        $r = [];

        $r[] = "public function has{$this->data->methodName}():bool { return \$this->colorData->has('{$this->data->fieldName}'); }";

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
        return [
            ItemWithColorDataTrait::class
        ];
    }
}