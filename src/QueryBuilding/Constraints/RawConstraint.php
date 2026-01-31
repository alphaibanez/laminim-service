<?php

namespace Lkt\QueryBuilding\Constraints;

class RawConstraint extends AbstractConstraint
{
    public function __toString(): string
    {
        return stripslashes($this->column);
    }
}