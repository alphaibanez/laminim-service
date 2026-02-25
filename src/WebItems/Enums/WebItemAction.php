<?php

namespace Lkt\WebItems\Enums;

enum WebItemAction: string
{
    case Page = 'pg';
    case List = 'ls';
    case Create = 'mk';
    case Read = 'r';
    case Update = 'up';
    case Duplicate = 'dup';
    case Drop = 'rm';
}
