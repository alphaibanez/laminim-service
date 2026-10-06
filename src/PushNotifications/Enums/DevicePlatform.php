<?php

namespace Lkt\PushNotifications\Enums;

enum DevicePlatform: int
{
    case Unknown = 0;
    case Android = 1;
    case iOS = 2;
}
