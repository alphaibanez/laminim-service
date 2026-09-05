<?php

namespace Lkt\Factory\Instantiator\Exceptions;

use Exception;

class MinLengthRequiredException extends Exception
{
    public function __construct($message = '', $val = 0, Exception $old = null)
    {
        parent::__construct($message, $val, $old);
    }

    public static function getInstance(string $value, int $limit)
    {
        $message = "MinLengthRequiredException: Datum '{$value}' requires at leats '{$limit}' characters";
        return new static($message);
    }
}