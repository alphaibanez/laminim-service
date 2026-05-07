<?php

namespace Lkt\Shop\Enums;

enum CouponDiscountType: int
{
    case Percent = 0;
    case Fixed = 1;
    case FreeShipping = 2;
}
