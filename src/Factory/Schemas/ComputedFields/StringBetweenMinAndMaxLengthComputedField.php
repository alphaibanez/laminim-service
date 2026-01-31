<?php

namespace Lkt\Factory\Schemas\ComputedFields;

class StringBetweenMinAndMaxLengthComputedField extends AbstractComputedField
{
    final public static function compute(string $field, int $min, int $max): static
    {
        $name = "{$field}BetweenMinAndMaxLength";
        $r = new static($name);
        $r->setField($field)->setValue([$min, $max]);
        return $r;
    }
}