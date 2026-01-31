<?php

namespace Lkt\Tools\Arrays;

function arrayAverage(array $a): float|int
{
    return array_sum($a) / count($a);
}