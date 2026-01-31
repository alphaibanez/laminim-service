<?php

namespace Lkt\Factory\Schemas\ComputedFields;

class StringBelowMaxLengthComputedField extends AbstractComputedField
{
    final public static function compute(string $field, int $value): static
    {
        $name = "{$field}BelowMaxLength";
        $r = new static($name);
        $r->setField($field)->setValue($value);
        return $r;
    }
}