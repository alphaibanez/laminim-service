<?php

namespace Lkt\Factory\Schemas\PrefabFields;

use Lkt\Factory\Schemas\Enums\PrefabRole;
use Lkt\Factory\Schemas\Fields\IntegerChoiceField;
use Lkt\WebPages\Enums\WebPageStatus;

class VisibilityStatusField
{
    final public static function define(string $name = 'status', string $column = 'status'): IntegerChoiceField
    {
        return IntegerChoiceField::enumChoice(WebPageStatus::class, $name, $column)->setPrefabRole(PrefabRole::VisibilityStatus);
    }
}