<?php

namespace Lkt\Tools\Parse;

function ensureStringInOptions(string $str, array $opts, int $defaultIndex = 0): string
{
    if (in_array($str, $opts, true)){
        return $str;
    }
    return $opts[$defaultIndex];
}