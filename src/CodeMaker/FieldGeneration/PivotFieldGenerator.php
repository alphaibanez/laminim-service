<?php

namespace Lkt\CodeMaker\FieldGeneration;

use Lkt\Attributes\LaminimUse;
use Lkt\CodeMaker\Interfaces\FieldGenerator;
use Lkt\CodeMaker\Traits\FieldGeneratorCommon;
use Lkt\Debug\VarDumper;
use Lkt\Factory\Fields\Interfaces\Field;
use Lkt\Factory\Instance\Traits\ItemWithPivotDataTrait;

#[LaminimUse]
class PivotFieldGenerator implements FieldGenerator
{
    use FieldGeneratorCommon;

    public function getGetters(): string
    {
        $r = [];

        $returnAnnotation = $this->getRelatedReturnAnnotationFormatted();

        if ($returnAnnotation) $r[] = "/** {$returnAnnotation}[] */";
        $r[] = "public function get{$this->data->methodName}(): array|null { return \$this->pivotData->getItems('{$this->data->fieldName}'); }";
        $r[] = "public function get{$this->data->methodName}Ids(): array|null { return \$this->pivotData->getItemsIds('{$this->data->fieldName}'); }";

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
        if ($this->mode === 'query') {
            return $this->getQueryBuilder();
        }

        return implode(' ', [
            $this->getGetters(),
            $this->getSetters(),
            $this->getCheckers(),
        ]);
    }

    protected function getQueryBuilder(): string
    {
        $r = [];
        $name = $this->field->getName();
        $methodName = ucfirst($name);
        $component = $this->schema->getComponent();

        foreach (['and', 'or'] as $constraint) {
            $r[] = "public function {$constraint}Any{$methodName}IdsLinked(array \${$name}) { return \$this->andAnyPivotLinked('{$name}', '{$component}', \${$name}); }";
            $r[] = "public function {$constraint}None{$methodName}IdsLinked(array \${$name}) { return \$this->andNonePivotLinked('{$name}', '{$component}', \${$name}); }";
            $r[] = "public function {$constraint}None{$methodName}Linked() { return \$this->andUnlinkedPivotLinked('{$name}', '{$component}'); }";
        }

        return implode(' ', $r);
    }

    public static function generateTraitsUsageCode(Field $field): array
    {
        return [
            ItemWithPivotDataTrait::class
        ];
    }
}