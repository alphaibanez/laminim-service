<?php

namespace Lkt\Instances;

use Lkt\Factory\Schemas\Schema;
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
        $schema = Schema::get(LktShoppingCoupon::COMPONENT);
        $query = $schema->getQueryBuilder()
            ->andBooleanTrue('is_active')
            ->andStringEqual('code', $code)
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