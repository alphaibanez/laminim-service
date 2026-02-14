<?php

namespace Lkt\Connectors\Exceptions;

use Exception;

class InvalidOpenExchangeRatesAppIdException extends \Exception
{
    public function __construct($message = '', $val = 0, Exception $old = null)
    {
        parent::__construct($message, $val, $old);
    }

    public static function getInstance(string $name): static
    {
        return new static("InvalidOpenExchangeRatesAppIdException: Connector '{$name}' needs a valid app id");
    }
}