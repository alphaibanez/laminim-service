<?php

namespace Lkt\WebPages\Enums;

enum WebPageStatus: int
{
    case NotDefined = 0;
    case Public = 1;
    case Draft = 2;
    case Scheduled = 3;
}