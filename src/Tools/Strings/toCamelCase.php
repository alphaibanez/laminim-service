<?php

namespace Lkt\Tools\Strings;


function toCamelCase(string $str = '', string $delimiter = '-'): string
{
    if (!str_contains($str, $delimiter)) {
        return $str;
    }
    $values = explode($delimiter, $str);
    $r = [];
    foreach ($values as $i => $value) {
        if ($i > 0) {
            $r[] = ucfirst(strtolower($value));
        } else {
            $r[] = strtolower($value);

        }
    }
    return implode('', $r);
}