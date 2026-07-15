<?php

namespace Lkt\CodeMaker\FieldGeneration;

use Lkt\CodeMaker\Interfaces\FieldGenerator;
use Lkt\CodeMaker\Traits\FieldGeneratorCommon;
use Lkt\Debug\VarDumper;

class IntegerChoiceFieldGenerator implements FieldGenerator
{
    use FieldGeneratorCommon;

    public function getGetters(): string
    {
        $r = [];

        if ($this->data->isMultiple) {
            $r[] = "/** @return int[] */";
            $r[] = "public function get{$this->data->methodName}():array { return \$this->multipleIntegerData->get('{$this->data->fieldName}'); }";

        } else {
            $r[] = "public function get{$this->data->methodName}():int { return \$this->integerData->get('{$this->data->fieldName}'); }";
        }


        return implode(' ', $r);
    }
    
    public function getSetters(): string
    {
        $r = [];

        $r[] = "/** @return {$this->data->selfReturningAnnotation} */";
        if ($this->data->isMultiple) {
            $r[] = "public function set{$this->data->methodName}(array \${$this->data->fieldName}):static { \$this->multipleIntegerData->set('{$this->data->fieldName}', \${$this->data->fieldName}); return \$this; }";

        } else {
            $enumClass = $this->getEnumChoiceClass();
            $r[] = "public function set{$this->data->methodName}(int{$enumClass} \${$this->data->fieldName}):static { \$this->integerData->set('{$this->data->fieldName}', \${$this->data->fieldName}); return \$this; }";
        }

        return implode(' ', $r);
    }

    public function getCheckers(): string
    {
        $r = [];

        $lowerFieldMethod = lcfirst($this->data->methodName);

        if ($this->data->isMultiple) {
            $r[] = "public function {$lowerFieldMethod}Is(array \$value):bool { return \$this->multipleIntegerData->equal('{$this->data->fieldName}', \$value); }";
            $r[] = "public function has{$this->data->methodName}():bool { return \$this->multipleIntegerData->has('{$this->data->fieldName}'); }";
            $r[] = "public function has{$this->data->methodName}In(array \$values):bool { return \$this->multipleIntegerData->in('{$this->data->fieldName}', \$values); }";

        } else {
            $enumClass = $this->getEnumChoiceClass();
            $r[] = "public function {$lowerFieldMethod}Is(int{$enumClass} \$value):bool { return \$this->integerData->equal('{$this->data->fieldName}', \$value); }";
            $r[] = "public function has{$this->data->methodName}():bool { return \$this->integerData->has('{$this->data->fieldName}'); }";
            $r[] = "public function has{$this->data->methodName}In(array \$values):bool { return \$this->integerData->in('{$this->data->fieldName}', \$values); }";
        }

        return implode(' ', $r);
    }

    public function getOptionsMethods(): string
    {
        $r = [];
        $optionsMethods = $this->getAllowedOptionsMethods();
        if (count($optionsMethods) > 0) {
            $lowerFieldMethod = lcfirst($this->data->methodName);
            foreach ($optionsMethods as $i => $option) {

                $optionVal = $this->data->options[$i];
                if (is_object($optionVal) && isset($optionVal->value)) {
                    $optionVal = $optionVal->value;
                } elseif (!is_int($optionVal)) {
                    $optionVal = (int)$optionVal;
                }

                if ($this->data->isMultiple) {
                    $r[] = "public function {$lowerFieldMethod}Is{$option}(): bool { return \$this->multipleIntegerData->equal('{$this->data->fieldName}', '{$optionVal}'); }";

                    $r[] = "/** @return {$this->data->selfReturningAnnotation} */";
                    $r[] = "public function set{$this->data->methodName}{$option}(): static { return \$this->multipleIntegerData->set('{$this->data->fieldName}', '{$optionVal}'); }";

                } else {
                    $r[] = "public function {$lowerFieldMethod}Is{$option}(): bool { return \$this->integerData->equal('{$this->data->fieldName}', '{$optionVal}'); }";

                    $r[] = "/** @return {$this->data->selfReturningAnnotation} */";
                    $r[] = "public function set{$this->data->methodName}{$option}(): static { return \$this->integerData->set('{$this->data->fieldName}', '{$optionVal}'); }";
                }


                if ($this->data->enabledEmptyPreset) {
                    $r[] = "public static function emptyWith{$this->data->methodName}EqualTo{$option}(): static { return static::getInstance()->{$this->data->methodName}{$option}(); }";
                }
            }
        }
        return implode(' ', $r);
    }

    public function getComparatorsInMethods(): string
    {
        $r = [];
        if (count($this->data->comparatorsIn) > 0) {
            $lowerFieldMethod = lcfirst($this->data->methodName);
            foreach ($this->data->comparatorsIn as $comparatorName => $options) {
                $upperComparatorName = ucfirst($comparatorName);

                $c = count($options);
                if ($c === 0) continue;

                $singleMode = false;
                if ($c === 1) {
                    $singleMode = true;
                    $optionsText = implode(',', $options);

                } else {
                    $optionsText = '[' .implode(',', $options) . ']';
                }

                $comparatorFunctionContent = $singleMode ? "equal" : 'in';
                $comparatorFunctionContent = $this->data->isMultiple
                    ? "multipleIntegerData->{$comparatorFunctionContent}"
                    : "integerData->{$comparatorFunctionContent}";

                $r[] = "public function {$lowerFieldMethod}Is{$upperComparatorName}(): bool { return \$this->{$comparatorFunctionContent}('{$this->data->fieldName}', {$optionsText}); }";

            }
        }
        return implode(' ', $r);
    }

    public function parse(): string
    {
        return implode(' ', [
            $this->getGetters(),
            $this->getSetters(),
            $this->getCheckers(),
            $this->getOptionsMethods(),
            $this->getComparatorsInMethods(),
        ]);
    }
}