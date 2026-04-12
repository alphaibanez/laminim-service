<?php

namespace Lkt\Debug;

class VarDumper
{
    public static function dump(mixed $data): void
    {
        $isCLI = php_sapi_name() === 'cli';

        if (!$isCLI) echo '<pre>';
        if ($data === true) {
            print_r('true');
        } elseif ($data === false) {
            print_r('false');
        } elseif ($data === null) {
            print_r('null');
        } else {
            print_r($data);
        }
        if (!$isCLI) echo '</pre>';
    }

    public static function die(mixed $data): void
    {
        static::dump($data);
        die();
    }
}