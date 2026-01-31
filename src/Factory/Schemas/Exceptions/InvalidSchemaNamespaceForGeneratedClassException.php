<?php

namespace Lkt\Factory\Schemas\Exceptions;

use Exception;

class InvalidSchemaNamespaceForGeneratedClassException extends Exception
{
    public function __construct($message = '', $val = 0, Exception $old = null)
    {
        if (!$message) {
            $message = 'InvalidSchemaNamespaceForGeneratedClassException';
        }
        parent::__construct($message, $val, $old);
    }
}