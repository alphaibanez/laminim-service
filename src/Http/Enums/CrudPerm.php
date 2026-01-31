<?php

namespace Lkt\Http\Enums;

enum CrudPerm: string
{
    case Create = 'create';
    case Read = 'read';
    case Update = 'update';
    case Drop = 'drop';
    case SwitchEditMode = 'switch-edit-mode';
}
