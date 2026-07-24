<?php

namespace Lkt\CodeMaker\FieldGeneration;

use Lkt\CodeMaker\Interfaces\FieldGenerator;
use Lkt\CodeMaker\Traits\FieldGeneratorCommon;
use Lkt\Factory\Instance\Traits\ItemWithForeignKeyDataTrait;
use Lkt\Factory\Schemas\Fields\AbstractField;

class ForeignKeyFieldGenerator implements FieldGenerator
{
    use FieldGeneratorCommon;

    public function getGetters(): string
    {
        $r = [];

        $returnAnnotation = $this->getRelatedReturnAnnotationFormatted();
        if ($returnAnnotation) {
            $r[] = "/** {$returnAnnotation} */";
        }
        $r[] = "public function get{$this->data->methodName}(){$this->getRelatedReturnTypeFormatted()} { return \$this->foreignKeyData->getItem('{$this->data->fieldName}'); }";
        $r[] = "public function get{$this->data->methodName}Id():int { return \$this->foreignKeyData->get('{$this->data->fieldName}'); }";

        return implode(' ', $r);
    }

    public function getSetters(): string
    {
        $r = [];

        $r[] = "/** @return {$this->data->selfReturningAnnotation} */";
        $r[] = "public function set{$this->data->methodName}Id(int \${$this->data->fieldName}):static { \$this->foreignKeyData->set('{$this->data->fieldName}', \${$this->data->fieldName}); return \$this; }";

        return implode(' ', $r);
    }

    public function getCheckers(): string
    {
        $r = [];

        $r[] = "public function has{$this->data->methodName};Id() :bool { return \$this->foreignKeyData->has('{$this->data->fieldName}'); }";
        $r[] = "public function has{$this->data->methodName};() :bool { return \$this->foreignKeyData->has('{$this->data->fieldName}'); }";

        return implode(' ', $r);
    }

    public function parse(): string
    {
        return implode(' ', [
            $this->getGetters(),
            $this->getSetters(),
        ]);
    }

    public static function generateTraitsUsageCode(AbstractField $field): array
    {
        return [
            ItemWithForeignKeyDataTrait::class
        ];
    }
}