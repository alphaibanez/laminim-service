<?php

namespace Lkt\Shop\Enums;

enum OrderStatus: int
{
    case Pending = 0;
    case Paid = 1;
    case Shipped = 2;
    case Finished = 3;
    case Cancelled = 4;
}
