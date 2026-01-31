<?php

namespace Lkt\QueryBuilding\DateIntervals;

class IntervalOfYears extends AbstractInterval
{
    public function toString(): string
    {
        if (!$this->isValid()) {
            return '';
        }
        $amount = abs($this->amount);
        if ($this->isPositive()) {
            return "+ INTERVAL {$amount} YEAR";
        }
        return "- INTERVAL {$amount} YEAR";
    }
}