<?php

namespace Lkt\Factory\Instantiator\Enums;

enum BatchInsertMode: int
{
    case onDuplicatedIgnore = 0;
    case onDuplicatedUpdate = 1;
    case onDuplicatedError = 2;
}