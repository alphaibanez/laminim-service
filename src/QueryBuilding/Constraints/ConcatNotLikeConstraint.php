<?php

namespace Lkt\QueryBuilding\Constraints;

use Lkt\QueryBuilding\Interfaces\QueryConstraint;

class ConcatNotLikeConstraint extends AbstractConcatConstraint implements QueryConstraint
{
    public function __toString(): string
    {
        $v = addslashes(stripslashes($this->value));
        if ($v === '') return '';
        $column = $this->getConcatBuild();
        return "{$column} NOT LIKE '%{$v}%'";
    }
}