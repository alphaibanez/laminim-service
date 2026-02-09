<?php

namespace Lkt\Http\Enums;

enum ToastPosition: int
{
    case TopLeft = 1;
    case TopCenter = 2;
    case TopRight = 3;
    case CenterLeft = 4;
    case CenterCenter = 5;
    case CenterRight = 6;
    case BottomLeft = 7;
    case BottomCenter = 8;
    case BottomRight = 9;
}