<?php

namespace Lkt\Exceptions;

use Exception;

class SilentHttpException extends Exception
{
    public readonly string $silentCode;

    public function __construct($message = '', $val = 0, Exception $old = null)
    {
        parent::__construct($message, $val, $old);
    }

    public static function getInstance(string $silentCode)
    {
        $r = new static();
        $r->silentCode = $silentCode;
        return $r;
    }
}