<?php

namespace Lkt\Factory\Schemas\Exceptions;

use Exception;

class SchemaNotDefinedException extends Exception
{
    public function __construct(string $component = null)
    {
        $message = "SchemaNotDefinedException: You're trying to access to a not defined schema: '{$component}'";
        parent::__construct($message, 0, null);
    }
}