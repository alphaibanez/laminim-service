<?php

namespace Lkt\CodeMaker\FieldGeneration;

use Lkt\CodeMaker\Interfaces\FieldGenerator;
use Lkt\CodeMaker\Traits\FieldGeneratorCommon;
use Lkt\Factory\Instance\Traits\ItemWithDateDataTrait;
use Lkt\Factory\Schemas\Fields\AbstractField;
use Lkt\Factory\Schemas\Fields\DateTimeField;

class DateTimeFieldGenerator implements FieldGenerator
{
    use FieldGeneratorCommon;

    public function getGetters(): string
    {
        $r = [];
        $r[] = "public function get{$this->data->methodName}():\Carbon\Carbon|null { return \$this->dateData->get('{$this->data->fieldName}'); }";
        $r[] = "public function get{$this->data->methodName}Formatted(string \$format = null):string|null { return \$this->dateData->format('{$this->data->fieldName}', \$format); }";
        $r[] = "public function get{$this->data->methodName}IntlFormatted(string \$format = null):string|null { return \$this->dateData->intlFormat('{$this->data->fieldName}', \$format); }";

        if ($this->data->field instanceof DateTimeField) {
            $formats = $this->data->field->getFormats();

            foreach ($formats as $name => $cfg) {
                $intl = $cfg[0];
                $format = $cfg[1];
                $method = $intl ? 'intlFormat' : 'format';
                $ucName = ucfirst($name);

                $r[] = "public function get{$this->data->methodName}FormattedAs{$ucName}():string|null { return \$this->dateData->{$method}('{$this->data->fieldName}', '{$format}'); }";

            }
        }
        return implode(' ', $r);
    }
    
    public function getSetters(): string
    {
        $r = [];

        $r[] = "/** @return {$this->data->selfReturningAnnotation} */";
        $r[] = "public function set{$this->data->methodName}(\${$this->data->fieldName}):static { \$this->dateData->set('{$this->data->fieldName}', \${$this->data->fieldName}); return \$this; }";
        return implode(' ', $r);
    }

    public function getCheckers(): string
    {
        $r = [];
        $r[] = "public function has{$this->data->methodName}():bool { return \$this->dateData->has('{$this->data->fieldName}'); }";

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
            ItemWithDateDataTrait::class
        ];
    }
}