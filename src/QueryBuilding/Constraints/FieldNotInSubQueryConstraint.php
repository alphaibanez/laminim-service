<?php

namespace Lkt\QueryBuilding\Constraints;

class FieldNotInSubQueryConstraint extends AbstractConstraint
{
    public function __toString(): string
    {
        $v = addslashes(stripslashes($this->value));
        return "{$this->column} NOT IN ({$v})";
    }
}