<?php

namespace Lkt\Tools\Strings;

function minLength(string $str, int $l): bool
{
    return strlen(trim($str)) >= $l;
}