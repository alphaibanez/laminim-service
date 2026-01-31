<?php

namespace Lkt\Factory\Schemas\ComputedFields;

class StringInComputedField extends AbstractComputedField
{
    final public static function compute(string $name, string $field, array $value = []): static
    {
        $r = new static($name);
        $r->setField($field)->setValue($value);
        return $r;
    }
}