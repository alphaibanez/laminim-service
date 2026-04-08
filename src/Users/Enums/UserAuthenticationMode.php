<?php

namespace Lkt\Users\Enums;

enum UserAuthenticationMode: int
{
    case Dynamic = 0;
    case Email = 1;
    case Identifier = 2;
}