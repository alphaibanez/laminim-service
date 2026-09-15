<?php

namespace Lkt\Attributes;

#[\Attribute]
class Experimental
{
    const VALUE = 'value';

    private $since;

    public function __construct($since = null)
    {
        $this->since = $since;
    }
}