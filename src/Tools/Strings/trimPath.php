<?php

namespace Lkt\Tools\Strings;

function trimPath(string $path): string
{
    return '/' . rtrim(ltrim(trim($path), '/'), '/');
}