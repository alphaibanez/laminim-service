<?php

namespace Lkt\Enums;

enum AccessTokenPurpose: int
{
    case Identifier = 1;
    case ChangePassword = 2;
}
