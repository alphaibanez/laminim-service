<?php

namespace Lkt\QueryBuilding\Constraints;

use Lkt\QueryBuilding\Interfaces\QueryConstraint;

class DatetimeEqualConstraint extends AbstractConstraint implements QueryConstraint
{
    public function __toString(): string
    {
        $column = $this->column;
        $value = $this->value;

        $prepend = $this->getTablePrepend();
        $v = addslashes(stripslashes($value));
        return "{$prepend}{$column} = '{$v}'";
    }
}