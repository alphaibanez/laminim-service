<?php

namespace Lkt\Factory\Schemas\Enums;

enum OnNonWritableSaveAttempt: int
{
    case Ignore = 1;
    case Error = 2;
}
