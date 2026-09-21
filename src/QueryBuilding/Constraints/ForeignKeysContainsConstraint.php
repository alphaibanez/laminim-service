<?php

namespace Lkt\QueryBuilding\Constraints;

use Lkt\QueryBuilding\Interfaces\QueryConstraint;

/**
 * @deprecated
 * @todo Remove after ForeignKeysField drop
 */
class ForeignKeysContainsConstraint extends AbstractConstraint implements QueryConstraint
{
    public function __toString(): string
    {
        $key = $this->column;
        $v = addslashes(stripslashes($this->value));

        if ($v !== '') {
            $t = [];
            $prepend = $this->getTablePrepend();
            $t[] = "{$prepend}{$key} LIKE '%;{$v};%'"; // in the middle
            $t[] = "{$prepend}{$key} LIKE '%;{$v}'"; // at the end
            $t[] = "{$prepend}{$key} LIKE '{$v};%'"; // at the beginning
            $t[] = "{$prepend}{$key} = '{$v}'"; // single value
            return '(' . implode(' OR ', $t) . ')';
        }
        return '';
    }
}