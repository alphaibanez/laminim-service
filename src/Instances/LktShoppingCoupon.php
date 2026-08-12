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

    public static function findActiveCode(string $code): static|null
    {
        $query = static::getQueryBuilder()
            ->andIsActiveIsTrue()
            ->andCodeEqual($code)
        ;

        $now = date('Y-m-d H:i:s');

        $timeStartConstraint = LktShoppingCouponWhere::startsAtIsNull()
            ->orStartsAtGreaterOrEqualThan($now);

        $timeEndConstraint = LktShoppingCouponWhere::endsAtIsNull()
            ->orEndsAtLowerOrEqualThan($now);

        $query
            ->andWhere($timeStartConstraint)
            ->andWhere($timeEndConstraint);


        return static::getOne($query);
    }
}