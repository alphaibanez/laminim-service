<?php

namespace Lkt\Instances;

use Lkt\Generated\GeneratedLktShoppingTax;

class LktShoppingTax extends GeneratedLktShoppingTax
{
    public function getFinalPrice(float $price): float
    {
        if ($this->taxTypeIsFixedAmount()) {
            return $price + $this->getTaxAmount();
        }

        if ($this->taxTypeIsPercentualAdd()) {
            $percent = ($this->getTaxAmount() * 100) / $price;
            return $price + $percent;
        }

        if ($this->taxTypeIsPercentualReverseCalc()) {
            return $price/(($this->getTaxAmount()/100)+1);
        }

        return $price;
    }
}