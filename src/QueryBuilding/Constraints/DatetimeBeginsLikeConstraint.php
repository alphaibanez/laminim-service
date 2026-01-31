<?php

namespace Lkt\QueryBuilding\Constraints;

class DatetimeBeginsLikeConstraint extends AbstractConstraint
{
    public function __toString(): string
    {
        $prepend = $this->getTablePrepend();
        $v = addslashes(stripslashes($this->value));
        return "{$prepend}{$this->column} LIKE '{$v}%'";
    }
}