<?php

namespace Lkt\Tools\Arrays;

function getArrayFirstPosition(array $arr = []): mixed
{
    return $arr[array_keys($arr)[0]];
}