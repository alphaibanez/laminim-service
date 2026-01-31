<?php

namespace Lkt\Factory\Schemas\Exceptions;

use Exception;

class MissedMandatoryValueException extends Exception
{
    public function __construct($message = '', $val = 0, Exception $old = null)
    {
        parent::__construct($message, $val, $old);
    }

    public static function getInstance(string $value)
    {
        return new static(
            "MissedMandatoryValueException: Missed required data: '{$value}'"
        );
    }
}