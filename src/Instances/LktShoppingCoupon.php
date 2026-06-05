<?php

namespace Lkt\Instances;

use Lkt\Generated\GeneratedLktShoppingCoupon;

class LktShoppingCoupon extends GeneratedLktShoppingCoupon
{
    const COMPONENT = 'lkt-shopping-coupon';

    public function getDiscount(float $price): float
    {
        $value = $this->getValue();
        if ($this->discountTypeIsFixed()) {
            return $value;
        }

        return $price * ($value/100);
    }
}