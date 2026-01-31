<?php

namespace Lkt\Tools\Parse;

function ensureStringFilled(string $str, string $default = ''): string
{
    if ($str === ''){
        return $default;
    }
    return $str;
}