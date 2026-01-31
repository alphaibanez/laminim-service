<?php

namespace Lkt\Tools\Arrays;

function arrayValuesRecursiveWithKeys(array $array = [], string $divider = '.', array $parentKeys = []): array
{
    $r = [];

    foreach ($array as $key => $value) {
        $t = array_merge($parentKeys, [$key]);
        if (is_array($value)) {
            $r = array_merge($r, arrayValuesRecursiveWithKeys($value, $divider, $t));
        } else {
            $k = implode($divider, $t);
            $r[$k] = $value;
        }
    }
    return $r;
}