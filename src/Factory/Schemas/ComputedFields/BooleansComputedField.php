<?php

namespace Lkt\Factory\Schemas\ComputedFields;

use Lkt\Factory\Schemas\Schema;

class BooleansComputedField extends AbstractComputedField
{
    protected array $allNeeded = [];

    final public static function allConditionsAreRequired(string $name, array $values = []): static
    {
        $r = new static($name);
        $r->allNeeded = $values;
        return $r;
    }

    public static function getAllConditionRequiredString(self $field, Schema $schema): string
    {
        $values = $field->allNeeded;
        $allRequiredConfig = [];
        foreach ($values as $key => $bool) {
            if (is_bool($bool)) {
                $datum = $schema->getField($key);
                $getter = $datum->getGetterForComputed();
                $str = "\$this->{$getter}()";
                if (!$bool) {
                    $str = "!{$str}";
                }
                $allRequiredConfig[$getter] = $str;
            }
        }
        $r = implode( ' && ', $allRequiredConfig);
        if ($r !== '') $r = "return {$r};";
        return $r;
    }

    public static function getAllConditionRequiredSetterString(self $field, Schema $schema): string
    {
        $values = $field->allNeeded;
        $allRequiredConfig = [];
        foreach ($values as $key => $bool) {
            if (is_bool($bool)) {
                $datum = $schema->getField($key);
                $setter = 'set'.ucfirst($datum->getName());
                $boolStr = (bool)$bool === true ? 'true' : 'false';
                $str = "{$setter}({$boolStr})";
                $allRequiredConfig[$setter] = $str;
            }
        }
        $r = implode( '->', $allRequiredConfig);
        if ($r !== '') $r = "return \$this->{$r};";
        return $r;
    }

    public static function getAllConditionRequiredQueryString(self $field, Schema $schema): string
    {
        $values = $field->allNeeded;
        $allRequiredConfig = [];
        foreach ($values as $key => $bool) {
            if (is_bool($bool)) {
                $datum = $schema->getField($key);
                $column = $datum->getColumn();
                $str = $bool ? ":CONSTRAINTBooleanTrue('$column')" : ":CONSTRAINTBooleanFalse('$column')";
                $allRequiredConfig[$column] = $str;
            }
        }
        $r = implode( '->', $allRequiredConfig);
        if ($r !== '') $r = "return \$this->{$r};";
        return $r;
    }

    public static function getAllConditionRequiredStaticQueryString(self $field, Schema $schema): string
    {
        $values = $field->allNeeded;
        $allRequiredConfig = [];
        foreach ($values as $key => $bool) {
            if (is_bool($bool)) {
                $datum = $schema->getField($key);
                $column = $datum->getColumn();
                $str = $bool ? "booleanTrue('$column')" : "booleanFalse('$column')";
                $allRequiredConfig[$column] = $str;
            }
        }
        $r = implode( '->and', $allRequiredConfig);
        if ($r !== '') $r = "return static::{$r};";
        return str_replace('andboolean', 'andBoolean', $r);
    }
}