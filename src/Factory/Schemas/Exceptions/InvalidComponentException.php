<?php

namespace Lkt\Factory\Schemas\Exceptions;

use Exception;

class InvalidComponentException extends Exception
{
    public function __construct($message = '', $val = 0, Exception $old = null)
    {
        if (!$message) {
            $message = 'InvalidComponentException: An schema component cannot be empty';
        }
        parent::__construct($message, $val, $old);
    }
}