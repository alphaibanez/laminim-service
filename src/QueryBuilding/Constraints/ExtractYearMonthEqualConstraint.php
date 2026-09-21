<?php

namespace Lkt\QueryBuilding\Constraints;

use Lkt\QueryBuilding\Interfaces\QueryConstraint;

class ExtractYearMonthEqualConstraint extends AbstractConstraint implements QueryConstraint
{
    public function __toString(): string
    {
        $column = $this->column;
        $value = $this->value;

        $prepend = $this->getTablePrepend();
        $v = addslashes(stripslashes($value));
        return "EXTRACT(YEAR_MONTH FROM {$prepend}{$column}) = '{$v}'";
    }
}