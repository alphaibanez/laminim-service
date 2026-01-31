<?php

namespace Lkt\QueryBuilding\Constraints;

class IsNullConstraint extends AbstractConstraint
{
    public function __toString(): string
    {
        $prepend = $this->getTablePrepend();
        return "{$prepend}{$this->column} IS NULL";
    }
}