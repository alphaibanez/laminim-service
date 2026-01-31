<?php

namespace Lkt\Factory\Schemas\Exceptions;

use Exception;

class DuplicatedValueException extends Exception
{
    public function __construct($message = '', $val = 0, Exception $old = null)
    {
        parent::__construct($message, $val, $old);
    }

    public static function getInstance(string $value)
    {
        return new static(
            "DuplicatedValueException: There is another element with this value: '{$value}'"
        );
    }
}