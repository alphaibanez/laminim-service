<?php

namespace Lkt\Enums;

enum AccessTokenDuration: int
{
    case Temporary = 1;
    case Forever = 2;
}
