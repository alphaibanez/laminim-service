<?php

namespace Lkt\Factory\Instance\Enums;

enum EmptyDataMode: int
{
    case OnlyNull = 0;
    case NullAndEmpty = 1;
}
