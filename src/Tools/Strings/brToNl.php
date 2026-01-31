<?php

namespace Lkt\Tools\Strings;

function brToNl(string $str = ''): string
{
    return \trim(\str_ireplace(['<br />', '<br>', '<br/>'], "\n", $str));
}