<?php
namespace Lkt\Tools\Arrays;

function arrayValuesRecursive(array $array = []): array
{
    $r = [];

    foreach ($array as $value) {
        if (is_array($value)) {
            $r = array_merge($r, arrayValuesRecursive($value));
        } else {
            $r[] = $value;
        }
    }
    return $r;
}