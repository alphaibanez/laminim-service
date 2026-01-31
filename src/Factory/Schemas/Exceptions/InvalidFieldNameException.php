<?php

namespace Lkt\Factory\Schemas\Exceptions;

use Exception;

class InvalidFieldNameException extends Exception
{
    public function __construct($message = '', $val = 0, Exception $old = null)
    {
        if (!$message) {
            $message = 'InvalidFieldNameException: An schema field cannot has an empty name';
        }
        parent::__construct($message, $val, $old);
    }
}