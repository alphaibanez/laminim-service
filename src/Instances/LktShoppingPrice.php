<?php

namespace Lkt\Instances;

use Lkt\Factory\Instantiator\Enums\CrudOperation;
use Lkt\Generated\GeneratedLktShoppingPrice;
use Lkt\Traits\WithComponentIdTrait;

class LktShoppingPrice extends GeneratedLktShoppingPrice
{
    const COMPONENT = 'lkt-shopping-price';

    use WithComponentIdTrait;

    public function prepareCrudData(array $data, CrudOperation|null $operation = null): array
    {
        if (isset($data['webItemName'])) {
            $data['componentId'] = $this->getComponentIdByWebItemName($data['webItemName']);
        }

        return $data;
    }

    public function getFinalPricePerUnit(): float
    {
        $shoppingTax = $this->getShoppingTax();
        $price = $this->getPricePerUnit();

        if ($shoppingTax) {
            if ($shoppingTax->taxTypeIsFixedAmount()) {
                return $price + $shoppingTax->getTaxAmount();
            }

            if ($shoppingTax->taxTypeIsPercentualAdd()) {
                return $price + $shoppingTax->getTaxAmount();
            }
        }

        return $price;
    }
}