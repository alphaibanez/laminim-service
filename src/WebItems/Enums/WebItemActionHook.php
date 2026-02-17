<?php

namespace Lkt\WebItems\Enums;

enum WebItemActionHook: int
{
    case PrepareQueryBuilder = 1;
    case Success = 2;
    case Fail = 3;
    case BeforeAction = 4;
    case TweakResponseData = 5;
}
