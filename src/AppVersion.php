<?php

namespace Lkt;

class AppVersion
{
    protected static string|null $APP_VERSION = null;
    protected static int|null $APP_VERSION_NUMBER = null;

    public static function readFromComposerFile(string $path): bool
    {
        if (file_exists($path)) {
            $content = json_decode(file_get_contents($path), true);
            if (isset($content['version'])) {
                static::$APP_VERSION = trim($content['version']);

                $arr = explode('.', static::$APP_VERSION);
                if (count($arr) !== 3) return 0;
                $major = (int)$arr[0];
                $minor = (int)$arr[1];
                $patch = (int)$arr[2];
                static::$APP_VERSION_NUMBER = (int)($major * 10000 + $minor * 100 + $patch);
                return true;
            }
        }
        return false;
    }

    public static function setVersion(string|int $major, string|int $minor = '', string|int $patch = ''): bool
    {
        $version = [];
        if (trim($major) !== '') $version[] = $major;
        if (trim($minor) !== '') $version[] = $minor;
        if (trim($patch) !== '') $version[] = $patch;
        $version = implode('.', $version);
        static::$APP_VERSION = $version;
        return true;
    }

    public static function get(): string
    {
        return trim(static::$APP_VERSION);
    }

    public static function getAsNumber(): int
    {
        return static::$APP_VERSION_NUMBER;
    }
}