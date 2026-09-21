<?php

namespace Lkt\QueryBuilding\Constraints;

use Lkt\QueryBuilding\Interfaces\QueryConstraint;

class DatetimeEndsLikeConstraint extends AbstractConstraint implements QueryConstraint
{
    public function __toString(): string
    {
        $prepend = $this->getTablePrepend();
        $v = addslashes(stripslashes($this->value));
        return "{$prepend}{$this->column} LIKE '%{$v}'";
    }
}