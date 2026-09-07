<?php

namespace Lkt\Factory\Instance\Enums;

enum RetrieveDataMode: int
{
    case Auto = -1;
    case Raw = 0;
    case Item = 1;
    case Ids = 2;
    case ItemOrAnonymous = 3;

    case FileName =  6;
    case FileExtension =  7;
    case FileContent =  8;
    case FileLastModified =  9;
    case FileSize =  10;
    case FileInternalPath =  11;
    case FilePublicPath =  12;
}