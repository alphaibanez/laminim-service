<?php

namespace Lkt\QueryBuilding\Constraints;

use Lkt\QueryBuilding\Interfaces\QueryConstraint;

class DecimalLowerOrEqualThanConstraint extends AbstractConstraint implements QueryConstraint
{
    public function __toString(): string
    {
        $v = addslashes(stripslashes((float)$this->value));
        $prepend = $this->getTablePrepend();
        return "{$prepend}{$this->column}<={$v}";
    }
}