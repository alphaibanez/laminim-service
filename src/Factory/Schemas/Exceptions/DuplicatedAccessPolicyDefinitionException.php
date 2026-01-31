<?php

namespace Lkt\Factory\Schemas\Exceptions;

use Exception;

class DuplicatedAccessPolicyDefinitionException extends Exception
{
    public function __construct($message = '', $val = 0, Exception $old = null)
    {
        parent::__construct($message, $val, $old);
    }

    public static function getInstance(string $schema, string $value)
    {
        return new static(
            "DuplicatedAccessPolicyDefinitionException: Schema '{$schema}' has another access policy defined with this name: '{$value}'"
        );
    }
}