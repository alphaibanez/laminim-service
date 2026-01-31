<?php

namespace Lkt\Tools\Arrays;

function implodeWithAND(array $data): string
{
    return trim(implode(' AND ', $data));
}