<?php

namespace Lkt\Factory\Instance\Enums;

enum RetrieveDataMode: int
{
    case Auto = -1;
    case Raw = 0;
    case Item = 1;
    case Ids = 2;
    case ItemOrAnonymous = 3;
}