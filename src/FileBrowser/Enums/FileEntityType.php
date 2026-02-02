<?php

namespace Lkt\FileBrowser\Enums;

enum FileEntityType: int
{
    case StorageUnit = 0;
    case Directory = 1;
    case Image = 2;
    case Video = 3;
    case File = 4;
}