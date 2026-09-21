<?php

namespace Lkt\QueryBuilding\Constraints;

use Lkt\QueryBuilding\Interfaces\QueryConstraint;

class FieldInSubQueryConstraint extends AbstractConstraint implements QueryConstraint
{
    public function __toString(): string
    {
        $v = $this->value;
        return "{$this->column} IN ({$v})";
    }
}