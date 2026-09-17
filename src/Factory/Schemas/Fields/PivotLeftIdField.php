<?php

namespace Lkt\Factory\Schemas\Fields;
use Lkt\Attributes\Deprecated;

/**
 * @deprecated
 */
#[Deprecated]
class PivotLeftIdField extends IntegerField
{
    public static function defineRelation(string $component, string $name, string $column = ''): IntegerField
    {
        return IntegerField::leftPivot($component, $name, $column);
    }
}