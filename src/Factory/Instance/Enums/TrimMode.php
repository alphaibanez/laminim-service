<?php

namespace Lkt\Factory\Instance\Enums;

enum TrimMode: int
{
    case None = 0;
    case Start = 1;
    case End = 2;
    case Full = 3;
}