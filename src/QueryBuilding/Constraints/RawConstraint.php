<?php

namespace Lkt\QueryBuilding\Constraints;

use Lkt\QueryBuilding\Interfaces\QueryConstraint;

class RawConstraint extends AbstractConstraint implements QueryConstraint
{
    public function __toString(): string
    {
        return stripslashes($this->column);
    }
}