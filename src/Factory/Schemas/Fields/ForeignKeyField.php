<?php

namespace Lkt\Factory\Schemas\Fields;

use Lkt\Attributes\Deprecated;

/**
 * @deprecated sse IntegerField::foreignKey instead
 */
#[Deprecated]
class ForeignKeyField extends IntegerField
{
    public static function defineRelation(string $component, string $name, string $column = ''): IntegerField
    {
        return IntegerField::foreignKey($component, $name, $column);
    }
}