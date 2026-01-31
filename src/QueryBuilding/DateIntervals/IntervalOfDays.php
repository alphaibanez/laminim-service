<?php

namespace Lkt\QueryBuilding\DateIntervals;

class IntervalOfDays extends AbstractInterval
{
    public function toString(): string
    {
        if (!$this->isValid()) {
            return '';
        }
        $amount = abs($this->amount);
        if ($this->isPositive()) {
            return "+ INTERVAL {$amount} DAY";
        }
        return "- INTERVAL {$amount} DAY";
    }
}