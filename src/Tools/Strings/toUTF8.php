<?php

namespace Lkt\Tools\Strings;

function toUTF8(string $str = ''): string
{
    return \htmlentities($str, ENT_QUOTES, 'utf-8');
}