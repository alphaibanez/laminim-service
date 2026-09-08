<?php

namespace Lkt\Factory\Fields\Enums;

enum OnParentDrop
{
    case Cascade;
    case SetNull;
    case SetDefault;
    case SetCustomValue;
    case Nothing;
}
