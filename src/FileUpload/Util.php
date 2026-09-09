<?php

namespace Lkt\FileUpload;

class Util
{
    /**
     * Ensure correct value for big integers
     * @param  integer $int
     * @return float
     */

    public static function fixIntegerOverflow($int)
    {
        if ($int < 0) {
            $int += 2.0 * (PHP_INT_MAX + 1);
        }

        return $int;
    }
}
