<?php

namespace Lkt\QueryBuilding\Constraints;

class StringNotConstraint extends AbstractConstraint
{
    public function __toString(): string
    {
        $column = $this->column;
        $value = $this->value;
        if (str_starts_with($value, 'COMPRESS(')) {
            return "{$column}!={$value}";
        }

        $v = addslashes(stripslashes($value));
        $prepend = $this->getTablePrepend();
        return "{$prepend}{$column}!='{$v}'";
    }
}