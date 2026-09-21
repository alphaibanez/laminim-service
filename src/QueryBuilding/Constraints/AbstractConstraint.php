<?php

namespace Lkt\QueryBuilding\Constraints;

use Lkt\Connectors\Interfaces\DatabaseConnector;
use Lkt\QueryBuilding\Traits\BaseQueryConstraint;

abstract class AbstractConstraint
{
    use BaseQueryConstraint;

    abstract public function __toString(): string;

    public function toString(DatabaseConnector|null $connector = null): string
    {
        return (String)$this;
    }
}