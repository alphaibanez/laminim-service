<?php

namespace Lkt\Factory\Schemas\Exceptions;

use Exception;

class InvalidFieldFilePathException extends Exception
{
    public function __construct($message = '', $val = 0, Exception $old = null)
    {
        if (!$message) {
            $message = 'InvalidFieldFilePathException: An schema field cannot has an empty file path';
        }
        parent::__construct($message, $val, $old);
    }
}