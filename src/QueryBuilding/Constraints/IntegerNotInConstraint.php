<?php

namespace Lkt\QueryBuilding\Constraints;

use Lkt\QueryBuilding\Interfaces\QueryConstraint;

class IntegerNotInConstraint extends AbstractConstraint implements QueryConstraint
{
    public function __toString(): string
    {
        if (count($this->value) === 0) {
            return '';
        }
        $values = array_map(function($v){ return addslashes(stripslashes((int)$v));}, $this->value);
        $value = "('".implode("','", $values)."')";
        $prepend = $this->getTablePrepend();
        return "{$prepend}{$this->column} NOT IN {$value}";
    }
}