<?php

namespace Lkt\Tools\System;

function isAbsolutePath(string $path) :bool
{
    if ($path === null || $path === '') {
        return false;
    }

    return $path[0] === DIRECTORY_SEPARATOR || preg_match('~\A[A-Z]:(?![^/\\\\])~i', $path) > 0;
}