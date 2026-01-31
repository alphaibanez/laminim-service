<?php

namespace Lkt\Users\Enums;

enum UserStatus: int
{
    case Undefined = -1;
    case Active = 0;
    case Inactive = 1;
    case Activating = 2;
    case Archived = 3;
    case Blocked = 4;

    public static function getChoiceOptions(): array
    {
        return [
            'undefined' => UserStatus::Undefined->value,
            'active' => UserStatus::Active->value,
            'inactive' => UserStatus::Inactive->value,
            'activating' => UserStatus::Activating->value,
            'archived' => UserStatus::Archived->value,
            'blocked' => UserStatus::Blocked->value,
        ];
    }
}