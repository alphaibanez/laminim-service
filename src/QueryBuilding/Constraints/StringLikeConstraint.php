<?php

namespace Lkt\QueryBuilding\Constraints;

class StringLikeConstraint extends AbstractConstraint
{
    public function __toString(): string
    {
        $v = addslashes(stripslashes($this->value));
        $prepend = $this->getTablePrepend();
        return "{$prepend}{$this->column} LIKE '%{$v}%'";
    }
}