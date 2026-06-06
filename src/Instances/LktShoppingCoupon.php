<?php

namespace Lkt\Instances;

use Lkt\Generated\GeneratedLktShoppingCoupon;
use Lkt\Generated\LktShoppingCouponWhere;

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

    public static function findActiveCode(string $code)
    {
        $query = static::getQueryCaller()
            ->andIsActiveIsTrue()
            ->andCodeEqual($code)
        ;

//        $timeStartConstraint = LktShoppingCouponWhere::startsAt();


        return static::getOne($query);
    }
}