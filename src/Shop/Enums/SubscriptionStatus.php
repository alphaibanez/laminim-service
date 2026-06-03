<?php

namespace Lkt\Shop\Enums;

enum SubscriptionStatus: int
{
    case Inactive = 0;
    case Active = 1;
    case Finished = 2;
    case Cancelling = 3;
    case Cancelled = 4;
}
