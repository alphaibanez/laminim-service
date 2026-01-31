<?php

namespace Lkt\Tools\Parse;

function explodeInputKeys(string $datum): array
{
    $r = explode(';', trim($datum));
    $r = array_filter($r, function ($id) { return trim($id) !== ''; });
    return array_values($r);
}