<?php

namespace Lkt\Tools\Arrays;

function arrayKeyPushUnique(array &$array, string $key, $data): void
{
    if (!array_key_exists($key, $array)) {
        $array[$key] = $data;
    }
}