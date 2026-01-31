<?php

namespace Lkt\QueryBuilding\Constraints;

class ExtractYearMonthEqualConstraint extends AbstractConstraint
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