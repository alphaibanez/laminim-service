<?php

namespace Lkt\QueryBuilding\Constraints;

class FieldInSubQueryConstraint extends AbstractConstraint
{
    public function __toString(): string
    {
        $v = $this->value;
        return "{$this->column} IN ({$v})";
    }
}