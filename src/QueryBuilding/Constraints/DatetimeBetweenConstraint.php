<?php

namespace Lkt\QueryBuilding\Constraints;

use Lkt\QueryBuilding\Interfaces\QueryConstraint;

class DatetimeBetweenConstraint extends AbstractConstraint implements QueryConstraint
{
    public function __toString(): string
    {
        if (count($this->value) < 2) {
            return '';
        }
        $prepend = $this->getTablePrepend();
        $values = array_map(function($v){ return addslashes(stripslashes($v));}, $this->value);

        return "{$prepend}{$this->column} BETWEEN '{$values[0]}' AND '{$values[1]}'";
    }
}