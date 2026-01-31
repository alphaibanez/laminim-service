<?php

namespace Lkt\Tools\Parse;

function humanReadableToBytes($input): float|int|null
{
    $number = (int)$input;
    $units = [
        'b' => 1,
        'k' => 1024,
        'm' => 1048576,
        'g' => 1073741824
    ];
    $unit = strtolower(substr($input, -1));
    if (isset($units[$unit])) {
        return $number * $units[$unit];
    } else {
        return null;
    }
}