<?php

namespace Lkt\Users\Enums;

enum PerformedAuthAction: int
{
    case SignIn = 1;
    case SignOut = 2;
    case RememberPassword = 3;
    case SignUp = 4;

    public static function getChoiceOptions(): array
    {
        return [
            'signIn' => PerformedAuthAction::SignIn->value,
            'signOut' => PerformedAuthAction::SignOut->value,
        ];
    }
}
