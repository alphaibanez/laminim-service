<?php

namespace Lkt\QueryBuilding\Constraints;

class ForeignKeysContainsConstraint extends AbstractConstraint
{
    public function __toString(): string
    {
        $key = $this->column;
        $v = addslashes(stripslashes($this->value));

        if ($v !== '') {
            $t = [];
            $prepend = $this->getTablePrepend();
            $t[] = "{$prepend}{$key} LIKE '%;{$v};%'";
            $t[] = "{$prepend}{$key} LIKE '%;{$v}'";
            $t[] = "{$prepend}{$key} LIKE '{$v};%'";
            $t[] = "{$prepend}{$key} = '{$v}'";
            return '(' . implode(' OR ', $t) . ')';
        }
        return '';
    }
}