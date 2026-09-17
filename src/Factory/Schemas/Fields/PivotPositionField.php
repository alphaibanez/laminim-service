<?php

namespace Lkt\Factory\Schemas\Fields;

/**
 * @deprecated
 */
class PivotPositionField extends IntegerField
{
    public static function define(string $name, string $column = ''): static
    {
        return static::position($name, $column);
    }
}