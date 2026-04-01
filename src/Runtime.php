<?php

namespace Lkt;

class Runtime
{
    protected static string|null $rootDir = null;

    public static function setRootDir(string $rootDir): void
    {
        static::$rootDir = $rootDir;
    }

    public static function getRootDir(): string|null
    {
        return static::$rootDir;
    }
}