<?php

namespace Lkt\QueryBuilding\Constraints;

use Lkt\QueryBuilding\Interfaces\QueryConstraint;

class StringEqualConstraint extends AbstractConstraint implements QueryConstraint
{
    public function __toString(): string
    {
        $column = $this->column;
        $value = $this->value;
        if (str_starts_with($value, 'COMPRESS(')) {
            return "{$column}={$value}";
        }

        $v = addslashes(stripslashes($value));
        $prepend = $this->getTablePrepend();
        return "{$prepend}{$column}='{$v}'";
    }
}