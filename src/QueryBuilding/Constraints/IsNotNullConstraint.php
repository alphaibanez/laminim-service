<?php

namespace Lkt\QueryBuilding\Constraints;

use Lkt\QueryBuilding\Interfaces\QueryConstraint;

class IsNotNullConstraint extends AbstractConstraint implements QueryConstraint
{
    public function __toString(): string
    {
        $prepend = $this->getTablePrepend();
        return "{$prepend}{$this->column} IS NOT NULL";
    }
}