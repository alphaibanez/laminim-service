<?php

namespace Lkt\CodeMaker\FieldGeneration;

use Lkt\Attributes\LaminimUse;
use Lkt\CodeMaker\Interfaces\FieldGenerator;
use Lkt\CodeMaker\Traits\FieldGeneratorCommon;
use Lkt\Factory\Fields\Interfaces\Field;
use Lkt\Factory\Instance\Traits\ItemWithBooleanDataTrait;

#[LaminimUse]
class BooleanFieldGenerator implements FieldGenerator
{
    use FieldGeneratorCommon;

    public function getGetters(): string
    {
        $r = [];

        $r[] = "public function {$this->data->fieldName}():bool|null { return \$this->booleanData->get('{$this->data->fieldName}'); }";

        return implode(' ', $r);
    }

    public function getSetters(): string
    {
        $r = [];

        $r[] = "/** @return {$this->data->selfReturningAnnotation} */";
        $r[] = "public function set{$this->data->methodName}(bool \${$this->data->fieldName}):static { \$this->booleanData->set('{$this->data->fieldName}', \${$this->data->fieldName}); return \$this; }";

        return implode(' ', $r);
    }

    public function getCheckers(): string
    {
        return '';
    }

    public function parse(): string
    {
        if ($this->mode === 'query') {
            return $this->getQueryBuilder();
        }
        return implode(' ', [
            $this->getGetters(),
            $this->getSetters(),
        ]);
    }

    protected function getQueryBuilder(): string
    {
        return '';
    }

    public static function generateTraitsUsageCode(Field $field): array
    {
        return [
            ItemWithBooleanDataTrait::class
        ];
    }
}