<?php

namespace Lkt\Tools\Color;

function decToHex(array $color): ?string
{
    if (count($color) === 3) {
        $r = dechex($color[0]);
        $g = dechex($color[1]);
        $b = dechex($color[2]);

        if (strlen($r) === 1) {
            $r = "0{$r}";
        }

        if (strlen($g) === 1) {
            $g = "0{$g}";
        }

        if (strlen($b) === 1) {
            $b = "0{$b}";
        }
        return "#{$r}{$g}{$b}";
    }

    return null;
}