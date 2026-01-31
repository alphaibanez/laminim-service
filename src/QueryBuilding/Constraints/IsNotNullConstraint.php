<?php

namespace Lkt\QueryBuilding\Constraints;

class IsNotNullConstraint extends AbstractConstraint
{
    public function __toString(): string
    {
        $prepend = $this->getTablePrepend();
        return "{$prepend}{$this->column} IS NOT NULL";
    }
}