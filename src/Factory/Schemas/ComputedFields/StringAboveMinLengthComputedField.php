<?php

namespace Lkt\Factory\Schemas\ComputedFields;

class StringAboveMinLengthComputedField extends AbstractComputedField
{
    final public static function compute(string $field, int $value = 0): static
    {
        $name = "{$field}AboveMinLength";
        $r = new static($name);
        $r->setField($field)->setValue($value);
        return $r;
    }
}