<?php

function arrayInArray(array $haystack = [], array $needle = []): bool
{
    if (in_array($needle, $haystack, true)) {
        return true;
    }
    return false;
}