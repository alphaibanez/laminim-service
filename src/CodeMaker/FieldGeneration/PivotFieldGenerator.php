<?php

namespace Lkt\CodeMaker\FieldGeneration;

use Lkt\CodeMaker\Interfaces\FieldGenerator;
use Lkt\CodeMaker\Traits\FieldGeneratorCommon;
use Lkt\Factory\Instance\Traits\ItemWithPivotDataTrait;
use Lkt\Factory\Schemas\Fields\AbstractField;

class PivotFieldGenerator implements FieldGenerator
{
    use FieldGeneratorCommon;

    public function getGetters(): string
    {
        $r = [];

        $returnAnnotation = $this->getRelatedReturnAnnotationFormatted();

        if ($returnAnnotation) $r[] = "/** {$returnAnnotation}[] */";
        $r[] = "public function get{$this->data->methodName}(): array|null { return \$this->pivotData->getItems('{$this->data->fieldName}'); }";

        return implode(' ', $r);
    }

    public function getSetters(): string
    {
        $r = [];
        $r[] = "public function link{$this->data->methodName}Id(\$id):static { \$this->pivotData->link('{$this->data->fieldName}', \$id); return \$this; }";
        $r[] = "public function unlink{$this->data->methodName}Id(\$id):static { \$this->pivotData->unlink('{$this->data->fieldName}', \$id); return \$this; }";
        return implode(' ', $r);
    }

    public function getCheckers(): string
    {
        $r = [];
        $r[] = "public function has{$this->data->methodName}():bool { return \$this->pivotData->has('{$this->data->fieldName}'); }";
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
            ItemWithPivotDataTrait::class
        ];
    }
}