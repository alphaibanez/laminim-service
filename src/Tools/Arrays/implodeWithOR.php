<?php

namespace Lkt\Tools\Arrays;


function implodeWithOR(array $data): string
{
    return trim(implode(' OR ', $data));
}