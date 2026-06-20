<?php

namespace Lkt\Factory\Instance\Enums;

enum InvalidDataMode: int
{
    case CastToNull = 0;
    case CastToEmpty = 1;
    case CastToType = 2;
}
