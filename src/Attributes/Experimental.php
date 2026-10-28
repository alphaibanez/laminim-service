<?php

namespace Lkt\Attributes;

#[\Attribute]
class Experimental
{
    const VALUE = 'value';

    private $since;
    private $message;

    public function __construct($since = null, $message = null)
    {
        $this->since = $since;
        $this->message = $message;
    }
}