<?php

namespace Lkt\Tools\Arrays;

function arrayRemoveDatum(array &$haystack, string $needle): void
{
    $i = array_search($needle, $haystack, true);
    if ($i !== false){
        unset($haystack[$i]);

        if (is_numeric($i)) {
            $haystack = array_values($haystack);
        }
    }
}