<?php

namespace Lkt\Instances;

use Lkt\Enums\LaminimComponent;
use Lkt\Factory\Schemas\Schema;
use Lkt\Generated\GeneratedLktShoppingOrder;
use Lkt\Shop\Enums\OrderStatus;

class LktShoppingOrder extends GeneratedLktShoppingOrder
{
    const COMPONENT = 'lkt-shopping-order';

    public function doStatusUpdate(OrderStatus $status): static
    {
        $this->setStatus($status)->save();

        $log = LktShoppingOrderStatusLog::getInstance();
        $log->feedAndSave([
            'order' => $this->getId(),
            'status' => $status->value,
        ]);

        return $this;
    }

    public function confirmPayment(LktShoppingOrderPayment $payment): static
    {
        $payment
            ->setPaidAt(time())
            ->setStatusCompleted()
            ->save();

        return $this;
    }

    public function addPayment(array $payload): LktShoppingOrderPayment
    {
        $payment = LktShoppingOrderPayment::getInstance();
        return $payment->feedAndSave([
            ...$payload,
            'order' => $this->getId(),
        ]);
    }

    /**
     * @param array $payload
     * @return LktShoppingOrderItem[]
     */
    public function addItems(array $payload): array
    {
        $items = [];
        foreach ($payload as $value) {
            $ins = LktShoppingOrderItem::getInstance();
            $ins->feed([
                ...$value,
                'order' => $this->getId(),
            ]);
            $items[] = $ins;
        }

        $targetSchema = Schema::get(LaminimComponent::ShoppingOrderItem->value);
        $batchActions = $targetSchema->getBatchActions($items);
        $batchActions->create();

        return $items;
    }

    public function linkSubscription(LktShoppingSubscription $subscription): bool
    {
        $current = $this->getSubscriptions();
        $needle = $subscription->getId();
        foreach ($current as $item) if ($item->getId() === $needle) return false;

        $this->linkPivot('subscriptions', $subscription->getId());
//        $this->linkPivot(LktShoppingOrderPivotSubscription::COMPONENT, $subscription->getId());
        return true;
    }

    public function applyCoupon(LktShoppingCoupon $coupon): bool
    {
        $currentCoupons = $this->getCoupons();
        $needle = $coupon->getId();
        foreach ($currentCoupons as $currentCoupon) if ($currentCoupon->getId() === $needle) return false;


        $this->linkPivot('coupons', $coupon->getId());
//        $this->linkPivot(LktShoppingOrderPivotCoupon::COMPONENT, $coupon->getId());
        return true;
    }

    public function reCalc(): static
    {
        $total = $this->getSubTotal();
        $discount = 0;

        $coupons = $this->getCoupons();
        $nonAccumulativeCoupons = array_filter($coupons, function (LktShoppingCoupon $c) {
            return $c->stackable();
        });

        // If there isn't any non stackable coupon,
        // then do accumulative discount
        if (count($nonAccumulativeCoupons) === 0) {

            // Sort coupons in order to apply first the fixed quantity discount
            usort($coupons, function (LktShoppingCoupon $a, LktShoppingCoupon $b) {
                if ($a->discountTypeIsFixed() && !$b->discountTypeIsFixed()) return -1;
                if (!$a->discountTypeIsFixed() && $b->discountTypeIsFixed()) return 1;
                return 0;
            });

            foreach ($coupons as $coupon) {
                $discount += $coupon->getDiscount($total);
                $total -= $discount;
            }

        // Apply only the first non stackable coupon
        } else {
            reset($nonAccumulativeCoupons);
            $coupon = $nonAccumulativeCoupons[0];
            $discount += $coupon->getDiscount($total);
            $total -= $discount;
        }

        return $this
            ->setTotal($total)
            ->setDiscountTotal($discount)
            ->save();
    }
}