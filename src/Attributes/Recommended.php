<?php

namespace Lkt\Attributes;

#[\Attribute]
class Recommended
{

    private $since;

    public function __construct($since = null)
    {
        $this->since = $since;
    }
}