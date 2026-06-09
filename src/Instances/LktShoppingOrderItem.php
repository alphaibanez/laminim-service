<?php

namespace Lkt\Instances;

use Lkt\Generated\GeneratedLktShoppingOrderItem;
use Lkt\Traits\WithComponentIdTrait;

class LktShoppingOrderItem extends GeneratedLktShoppingOrderItem
{
    const COMPONENT = 'lkt-shopping-order-item';

    use WithComponentIdTrait;
}