<?php

namespace Lkt\QueryBuilding\Constraints;

use Lkt\QueryBuilding\Interfaces\QueryConstraint;

class ConcatEqualConstraint extends AbstractConcatConstraint implements QueryConstraint
{
    public function __toString(): string
    {
        $v = addslashes(stripslashes($this->value));
        $column = $this->getConcatBuild();
        return "{$column}='{$v}'";
    }
}