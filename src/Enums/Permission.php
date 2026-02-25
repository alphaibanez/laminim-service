<?php

namespace Lkt\Enums;

enum Permission: string
{
    case List = 'ls';
    case Create = 'mk';
    case Read = 'r';
    case Update = 'up';
    case Drop = 'rm';
}