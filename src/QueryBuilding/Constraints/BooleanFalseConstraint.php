<?php

namespace Lkt\QueryBuilding\Constraints;

class BooleanFalseConstraint extends AbstractConstraint
{
    public function __toString(): string
    {
        $prepend = $this->getTablePrepend();
        return "{$prepend}{$this->column}=0";
    }
}