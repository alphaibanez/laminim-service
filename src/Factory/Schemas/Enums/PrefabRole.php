<?php

namespace Lkt\Factory\Schemas\Enums;

enum PrefabRole: int
{
    case None = 0;
    case AccessLevel = 1;
    case VisibilityStatus = 2;
    case RelatedFileEntities = 3;
}
