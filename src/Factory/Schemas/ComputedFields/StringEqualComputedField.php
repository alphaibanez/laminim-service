<?php

namespace Lkt\Factory\Schemas\ComputedFields;


/**
 * @deprecated Use StringChoice field in order to match options
 */
class StringEqualComputedField extends AbstractComputedField
{
    final public static function compute(string $name, string $field, string $value = ''): static
    {
        $r = new static($name);
        $r->setField($field)->setValue($value);
        return $r;
    }
}