<?php

namespace Lkt\Users\Enums;

enum RoleCapability: int
{
    case Disabled = 0;
    case Owned = 1;
    case All = 2;

    public static function getChoiceOptions(): array
    {
        return [
            'disabled' => RoleCapability::Disabled->value,
            'owned' => RoleCapability::Owned->value,
            'all' => RoleCapability::All->value,
        ];
    }
}