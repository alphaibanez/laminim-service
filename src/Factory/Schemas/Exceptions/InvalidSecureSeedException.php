<?php

namespace Lkt\Factory\Schemas\Exceptions;

use Exception;

class InvalidSecureSeedException extends Exception
{
    public function __construct($message = '', $val = 0, Exception $old = null)
    {
        if (!$message) {
            $message = 'InvalidSecureSeedException: Encrypt fields must have a non empty secure seed';
        }
        parent::__construct($message, $val, $old);
    }
}