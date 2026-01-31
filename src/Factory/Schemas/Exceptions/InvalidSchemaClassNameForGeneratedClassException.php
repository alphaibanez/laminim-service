<?php

namespace Lkt\Factory\Schemas\Exceptions;

use Exception;

class InvalidSchemaClassNameForGeneratedClassException extends Exception
{
    public function __construct($message = '', $val = 0, Exception $old = null)
    {
        if (!$message) {
            $message = 'InvalidSchemaClassNameForGeneratedClassException';
        }
        parent::__construct($message, $val, $old);
    }
}