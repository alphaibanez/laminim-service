<?php

namespace Lkt\Tools\Arrays;

function digArray(array $haystack, string $key): mixed
{
    $walk = explode('.', $key);
    $dig = $haystack;
    foreach ($walk as $step) {
        if (is_array($dig)) {
            $dig = $dig[$step];
        } else {
            break;
        }
    }

    return $dig;
}