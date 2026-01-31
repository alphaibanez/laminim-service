<?php

namespace Lkt;
class DataMixer
{
    protected static array $mixes = [];

    public static function add(string $key, callable $callable): void
    {
        static::$mixes[$key][] = $callable;
    }

    public static function mixData(string $key, array $params = []): array
    {
        $x = static::$mixes[$key];
        if (!$x) {
            return [];
        }

        $r = [];
        foreach ($x as $y) {
            $handled = call_user_func_array($y, [$params]);
            if (is_array($handled)) {
                $r = array_merge($r, $handled);
            }
        }

        return $r;
    }

    public static function mix(string $key, array $params = []): array
    {
        $x = static::$mixes[$key];
        if (!$x) {
            return [];
        }

        $r = [];
        foreach ($x as $y) {
            $handled = call_user_func_array($y, $params);
            if (is_array($handled)) {
                $r = array_merge($r, $handled);
            }
        }

        return $r;
    }
}