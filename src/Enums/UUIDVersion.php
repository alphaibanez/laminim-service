<?php

namespace Lkt\Enums;

enum UUIDVersion: int
{
    case V3 = 3;
    case V4 = 4;
    case V5 = 5;
    case V7 = 7;
}
