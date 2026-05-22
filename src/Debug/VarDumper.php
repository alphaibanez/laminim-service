<?php

namespace Lkt\Debug;

class VarDumper
{
    public static function dump(...$args): void
    {
        $isCLI = php_sapi_name() === 'cli';

        foreach ($args as $data) {
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
    }

    public static function die(...$args): void
    {
        static::dump($args);
        die();
    }
}