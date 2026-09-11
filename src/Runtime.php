<?php

namespace Lkt;

use Lkt\Context\RuntimeContext;

class Runtime
{
    public static function setRootDir(string $rootDir): void
    {
        RuntimeContext::$rootDir = $rootDir;
    }

    public static function getRootDir(): string|null
    {
        return RuntimeContext::$rootDir;
    }
}