<?php

namespace Lkt\Attributes;

#[\Attribute]
class Stable
{
    private $since;

    public function __construct($since = null)
    {
        $this->since = $since;
    }
}