<?php

namespace Lkt\PushNotifications\Enums;

enum DeliveryStatus: int
{
    case Pending = 0;
    case Sent = 1;
    case Opened = 2;
}
