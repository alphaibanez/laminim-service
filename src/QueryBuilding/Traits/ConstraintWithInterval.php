<?php

namespace Lkt\QueryBuilding\Traits;

use Lkt\QueryBuilding\DateIntervals\AbstractInterval;

trait ConstraintWithInterval
{
    protected $interval = null;

    public function setInterval(AbstractInterval $interval = null): self
    {
        $this->interval = $interval;
        return $this;
    }
}