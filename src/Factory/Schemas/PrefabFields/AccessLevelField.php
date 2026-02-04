<?php

namespace Lkt\Factory\Schemas\PrefabFields;

use Lkt\Factory\Schemas\Fields\IntegerChoiceField;
use Lkt\Http\Enums\AccessLevel;

class AccessLevelField
{
    final public static function define(string $name = 'accessLevel', string $column = 'access_level'): IntegerChoiceField
    {
        return IntegerChoiceField::enumChoice(AccessLevel::class, $name, $column)->setPrefabRole('accessLevel');
    }
}