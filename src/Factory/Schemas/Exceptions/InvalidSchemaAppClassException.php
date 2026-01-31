<?php

namespace Lkt\Factory\Schemas\Exceptions;

use Exception;

class InvalidSchemaAppClassException extends Exception
{
    public function __construct($message = '', $val = 0, Exception $old = null)
    {
        if (!$message) {
            $message = 'InvalidSchemaAppClassException: An schema class name cannot be empty';
        }
        parent::__construct($message, $val, $old);
    }
}