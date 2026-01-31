<?php

namespace Lkt\Tools\Time;

function yesterday(string $format): string
{
    return date($format, strtotime('-1 days'));
}