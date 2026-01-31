<?php

namespace Lkt\Tools\Arrays;

function arrayPushUnique(array &$array, $data): void
{
    if (!in_array($data, $array, true)) {
        $array[] = $data;
    }
}