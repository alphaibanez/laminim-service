<?php

namespace Lkt\Instances;

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
}