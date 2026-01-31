<?php

namespace Lkt\Tools\Strings;

function removeDuplicatedWhiteSpaces(string $str = ''): string
{
    return preg_replace('/\s+/', ' ', $str);
}