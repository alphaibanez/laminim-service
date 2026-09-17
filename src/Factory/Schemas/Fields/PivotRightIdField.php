<?php

namespace Lkt\Factory\Schemas\Fields;

use Lkt\Attributes\Deprecated;

/**
 * @deprecated
 */
#[Deprecated]
class PivotRightIdField extends IntegerField
{
    public static function defineRelation(string $component, string $name, string $column = ''): IntegerField
    {
        return IntegerField::rightPivot($component, $name, $column);
    }
}