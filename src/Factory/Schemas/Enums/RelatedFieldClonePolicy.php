<?php

namespace Lkt\Factory\Schemas\Enums;

enum RelatedFieldClonePolicy: int
{
    case Ignore = 1;
    case KeepReferences = 2;
    case CloneReferences = 3;
}