<?php

namespace Lkt\Tools\Parse;

function clearInputKeys($datum): string
{
    if (!is_array($datum)) {
        return trim($datum);
    }
    return trim(implode(';', $datum));
}