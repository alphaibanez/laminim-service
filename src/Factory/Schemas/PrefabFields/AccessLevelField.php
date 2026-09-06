<?php

namespace Lkt\Factory\Schemas\PrefabFields;

use Lkt\Factory\Schemas\Enums\PrefabRole;
use Lkt\Factory\Schemas\Fields\IntegerField;
use Lkt\Http\Enums\AccessLevel;

class AccessLevelField
{
    final public static function define(string $name = 'accessLevel', string $column = 'access_level'): IntegerField
    {
        return IntegerField::enumChoice(AccessLevel::class, $name, $column)->setPrefabRole(PrefabRole::AccessLevel);
    }
}