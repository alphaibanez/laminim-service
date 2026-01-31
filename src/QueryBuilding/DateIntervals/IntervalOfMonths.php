<?php

namespace Lkt\QueryBuilding\DateIntervals;

class IntervalOfMonths extends AbstractInterval
{
    public function toString(): string
    {
        if (!$this->isValid()) {
            return '';
        }
        $amount = abs($this->amount);
        if ($this->isPositive()) {
            return "+ INTERVAL {$amount} MONTH";
        }
        return "- INTERVAL {$amount} MONTH";
    }
}