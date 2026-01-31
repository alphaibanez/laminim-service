<?php

namespace Lkt\QueryBuilding\Constraints;

use Lkt\QueryBuilding\DateIntervals\AbstractInterval;
use Lkt\QueryBuilding\Traits\ConstraintWithInterval;

class DatetimeGreaterThanNowConstraint extends AbstractConstraint
{
    use ConstraintWithInterval;

    public function __toString(): string
    {
        $column = $this->column;
        $v = 'NOW()';
        if ($this->interval instanceof AbstractInterval) {
            $v .= $this->interval->toString();
        }
        $prepend = $this->getTablePrepend();
        return "{$prepend}{$column} >= {$v}";
    }
}