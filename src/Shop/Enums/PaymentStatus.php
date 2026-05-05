<?php

namespace Lkt\Shop\Enums;

enum PaymentStatus: int
{
    case Pending = 1;
    case Completed = 2;
    case Failed = 3;
}
