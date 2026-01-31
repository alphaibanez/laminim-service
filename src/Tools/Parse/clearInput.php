<?php

namespace Lkt\Tools\Parse;

function clearInput($value): string
{
    return addslashes(stripslashes(trim($value)));
}