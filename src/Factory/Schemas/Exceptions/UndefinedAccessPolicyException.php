<?php

namespace Lkt\Factory\Schemas\Exceptions;

use Exception;

class UndefinedAccessPolicyException extends Exception
{
    public function __construct($message = '', $val = 0, Exception $old = null)
    {
        parent::__construct($message, $val, $old);
    }

    public static function getInstance(string $schema, string $value)
    {
        return new static(
            "UndefinedAccessPolicyException: Trying to access to undefined access policy '{$value}' at '{$schema}' schema"
        );
    }
}