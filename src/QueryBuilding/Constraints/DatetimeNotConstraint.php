<?php

namespace Lkt\QueryBuilding\Constraints;

use Lkt\QueryBuilding\Interfaces\QueryConstraint;

class DatetimeNotConstraint extends AbstractConstraint implements QueryConstraint
{
    public function __toString(): string
    {
        $column = $this->column;
        $value = $this->value;

        $v = addslashes(stripslashes($value));
        $prepend = $this->getTablePrepend();
        return "{$prepend}{$column} != '{$v}'";
    }
}