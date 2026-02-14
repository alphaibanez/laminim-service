<?php

namespace Lkt\FileBrowser\Enums;

enum BitUnit: string
{
    case Bit = 'b';
    case Byte = 'B';
    case KiloByte = 'KB';
    case MegaByte = 'MB';
    case GigaByte = 'GB';
    case TeraByte = 'TB';
    case PetaByte = 'PB';
    case ExaByte = 'EB';
    case ZettaByte = 'ZB';
    case YottaByte = 'YB';
}
