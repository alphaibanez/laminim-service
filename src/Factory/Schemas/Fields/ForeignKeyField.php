<?php

namespace Lkt\Factory\Schemas\Fields;

use Lkt\Attributes\Deprecated;

/**
 * @deprecated sse IntegerField::foreignKey instead
 */
#[Deprecated]
class ForeignKeyField extends IntegerField
{
    public static function defineRelation(string $component, string $name, string $column = ''): static
    {
        return static::foreignKey($component, $name, $column);
    }
}