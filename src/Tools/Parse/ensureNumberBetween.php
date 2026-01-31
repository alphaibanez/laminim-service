<?php

namespace Lkt\Tools\Parse;

function ensureNumberBetween($number, $min, $max)
{
    if ($number > $max) {
        $number = $max;
    }

    if ($number < $min) {
        $number = $min;
    }

    return $number;
}