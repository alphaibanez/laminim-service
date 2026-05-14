<?php

namespace Lkt\Instances;

use Lkt\Debug\VarDumper;
use Lkt\Generated\GeneratedLktShoppingOrder;
use Lkt\Shop\Enums\OrderStatus;

class LktShoppingOrder extends GeneratedLktShoppingOrder
{
    const COMPONENT = 'lkt-shopping-order';

    public function doStatusUpdate(OrderStatus $status): static
    {
        $this->setStatus($status)->save();

        $log = LktShoppingOrderStatusLog::getInstance();
        $log->autoCreate([
            'order' => $this->getId(),
            'status' => $status->value,
        ]);

        return $this;
    }

    public function addPayment(array $payload): LktShoppingOrderPayment
    {
        $payment = LktShoppingOrderPayment::getInstance();
        return $payment->autoCreate($payload);
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
            $ins::feedInstance($ins, $value);
            $items[] = $ins;
        }

        $batchActions = LktShoppingOrderItem::getBatchActions($items);
        $batchActions->create();

        return $items;
    }
}