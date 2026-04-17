<?php

namespace Lkt\Tools\Arrays;

function arrayValuesRecursiveWithKeys(array $array = [], string $divider = '.', array $parentKeys = []): array
{
    $r = [];

    foreach ($array as $key => $value) {
        $t = array_merge($parentKeys, [$key]);
        if (is_array($value)) {
            $temp = arrayValuesRecursiveWithKeys($value, $divider, $t);
            foreach ($temp as $k => $v) $r[$k] = $v;
        } else {
            $k = implode($divider, $t);
            $r[$k] = $value;
        }
    }
    return $r;
}