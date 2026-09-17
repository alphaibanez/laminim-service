<?php

namespace Lkt\Factory\Fields\Enums;

enum IntegerFieldType
{
    case Integer;
    case ForeignKey;
    case LeftPivot;
    case RightPivot;
    case Position;
}
