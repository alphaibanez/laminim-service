<?php

namespace Lkt\Debug;

class VarDumper
{
    public static function dump(mixed $data): void
    {
        $isCLI = php_sapi_name() === 'cli';

        if (!$isCLI) echo '<pre>';
        print_r($data);
        if (!$isCLI) echo '</pre>';
    }

    public static function die(mixed $data): void
    {
        static::dump($data);
        die();
    }
}