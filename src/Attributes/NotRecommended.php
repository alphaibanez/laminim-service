<?php

namespace Lkt\Attributes;

#[\Attribute]
class NotRecommended
{

    private $message;
    private $since;

    public function __construct($message = null, $since = null)
    {
        $this->message = $message;
        $this->since = $since;
    }
}