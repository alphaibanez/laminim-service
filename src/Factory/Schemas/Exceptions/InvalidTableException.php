<?php

namespace Lkt\Factory\Schemas\Exceptions;

use Exception;

class InvalidTableException extends Exception
{
    public function __construct($message = '', $val = 0, Exception $old = null)
    {
        if (!$message) {
            $message = 'InvalidTableException: An schema table cannot be empty';
        }
        parent::__construct($message, $val, $old);
    }
}