<?php

namespace Lkt\PushNotifications\Enums;

enum NotificationStatus: int
{
    case Pending = 0;
    case Processing = 1;
    case Sent = 2;
}
