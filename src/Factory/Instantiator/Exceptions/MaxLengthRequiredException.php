<?php

namespace Lkt\Factory\Instantiator\Exceptions;

use Exception;

class MaxLengthRequiredException extends Exception
{
    public function __construct($message = '', $val = 0, Exception $old = null)
    {
        parent::__construct($message, $val, $old);
    }

    public static function getInstance(string $value, int $limit)
    {
        $message = "MaxLengthRequiredException: Datum '{$value}' cannot have more than '{$limit}' characters";
        return new static($message);
    }
}