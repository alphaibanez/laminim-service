<?php

namespace Lkt\Factory\Schemas\Exceptions;

use Exception;

class InvalidItemDataAssignException extends Exception
{
    public function __construct($message = '', $val = 0, Exception $old = null)
    {
        parent::__construct($message, $val, $old);
    }

    public static function missingField(string $value)
    {
        return new static(
            "InvalidItemDataAssignException: Attempted to set a key missed in schema config: '{$value}'"
        );
    }
}