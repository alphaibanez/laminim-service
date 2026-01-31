<?php

namespace Lkt\QueryBuilding\Constraints;

class ConcatNotConstraint extends AbstractConcatConstraint
{
    public function __toString(): string
    {
        $v = addslashes(stripslashes($this->value));
        $column = $this->getConcatBuild();
        return "{$column}!='{$v}'";
    }
}