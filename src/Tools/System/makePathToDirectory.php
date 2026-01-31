<?php

namespace Lkt\Tools\System;

function makePathToDirectory(string $path): void
{
    if (!is_dir($path) && !mkdir($path, 0755, true)) {
        throw new \RuntimeException(sprintf('Directory "%s" was not created', $path));
    }
}