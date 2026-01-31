<?php

namespace Lkt\WebPages\Enums;

enum WebPageStatus: int
{
    case Public = 1;
    case Draft = 2;
    case Scheduled = 3;
    case Confidential = 4;
}