<?php

namespace Lkt\Factory\Fields\Enums;

enum EncryptAlgorithm: int
{
    case None = 0;
    case SHA256 = 1;
    case SHA256Hash = 2;
}