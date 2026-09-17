<?php

namespace Lkt\Factory\Schemas\Fields;

class PivotRightIdField extends IntegerField
{
    public static function defineRelation(string $component, string $name, string $column = ''): static
    {
        return static::foreignKey($component, $name, $column);
    }
}