<?php

namespace Lkt\PushNotifications\Enums;

enum QueuePriority: int
{
    case Low = 0;
    case Medium = 1;
    case High = 3;
    case Urgent = 4;
}
