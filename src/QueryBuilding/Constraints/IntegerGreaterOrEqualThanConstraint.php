<?php

namespace Lkt\QueryBuilding\Constraints;

class IntegerGreaterOrEqualThanConstraint extends AbstractConstraint
{
    public function __toString(): string
    {
        $v = addslashes(stripslashes((int)$this->value));
        $prepend = $this->getTablePrepend();
        return "{$prepend}{$this->column}>={$v}";
    }
}