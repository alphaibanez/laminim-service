<?php

namespace Lkt\QueryBuilding\Constraints;

class SubQueryCountEqualConstraint extends AbstractConstraint
{
    public function __toString(): string
    {
        $v = addslashes(stripslashes($this->value));
        return "{$v} = ({$this->column})";
    }
}