<?php

namespace Lkt\WebItems\Enums;

enum WebItemActionHook: int
{
    case PrepareQueryBuilder = 1;
    case Success = 2;
    case Fail = 3;
}
