<?php

namespace Lkt\Attributes;

#[\Attribute]
class Deprecated
{
    public readonly ?string $message;

    public readonly ?string $since;

    public function __construct(?string $message = null, ?string $since = null)
    {
        $this->message = $message;
        $this->since = $since;
    }
}