<?php

namespace Lkt\Tools\Strings;

function removeWhiteSpaces(string $str = ''): string
{
    return preg_replace('/\s+/', '', $str);
}