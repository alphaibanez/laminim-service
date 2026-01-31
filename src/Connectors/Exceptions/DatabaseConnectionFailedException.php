<?php

namespace Lkt\Connectors\Exceptions;

use Exception;

class DatabaseConnectionFailedException extends \Exception
{
    public function __construct($message = '', $val = 0, Exception $old = null)
    {
        parent::__construct($message, $val, $old);
    }

    public static function getInstance(Exception $exception): static
    {
        return new static("DatabaseConnectionFailedException: Connection to database failed", 0, $exception);
    }
}