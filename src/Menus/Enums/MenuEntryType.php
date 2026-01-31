<?php

namespace Lkt\Menus\Enums;

enum MenuEntryType: int
{
    case RelativeUrl = 1;
    case FullUrl = 2;
    case WebPage = 3;
    case WebPages = 4;
    case WebItem = 5;
    case WebItems = 6;

    case RouterRoute = 7;
    case Header = 8;
    case Parent = 9;
}
