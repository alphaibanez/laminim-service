<?php

namespace Lkt\WebItems\Enums;

enum WebItemAdminMenuRegister: int
{
    case Never = 1;
    case ValidAdminRole = 2;
    case OnlyAdministrator = 3;
}
