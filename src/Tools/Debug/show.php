<?php

namespace Lkt\Tools\Debug;

function show($data): void
{
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}