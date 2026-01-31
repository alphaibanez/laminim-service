<?php

namespace Lkt\QueryBuilding\Constraints;

class IntegerInConstraint extends AbstractConstraint
{
    public function __toString(): string
    {
        if (count($this->value) === 0) {
            return '';
        }
        $values = array_map(function($v){ return addslashes(stripslashes((int)$v));}, $this->value);
        $value = "('".implode("','", $values)."')";
        $prepend = $this->getTablePrepend();
        return "{$prepend}{$this->column} IN {$value}";
    }
}