<?php

namespace Lkt\WebPages\Enums;

enum WebElementType: int
{
    case LktText = 1;
    case LktTextBox = 2;
    case LktLayout = 3;
    case LktLayoutBox = 4;
    case LktTextAccordion = 5;
    case LktLayoutAccordion = 6;
    case LktTextBanner = 7;
    case LktLayoutBanner = 8;
    case LktAnchor = 9;
    case LktButton = 10;
    case LktHeader = 11;
    case LktIcon = 12;
    case LktIcons = 13;
    case LktImage = 14;
}