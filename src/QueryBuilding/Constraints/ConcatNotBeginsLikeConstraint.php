<?php

namespace Lkt\QueryBuilding\Constraints;

class ConcatNotBeginsLikeConstraint extends AbstractConcatConstraint
{
    public function __toString(): string
    {
        $v = addslashes(stripslashes($this->value));
        if ($v === '') return '';
        $column = $this->getConcatBuild();
        return "{$column} NOT LIKE '{$v}%'";
    }
}