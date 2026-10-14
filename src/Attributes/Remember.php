<?php

namespace Lkt\Attributes;

#[\Attribute(\Attribute::IS_REPEATABLE)]
class Remember
{
    public readonly ?string $message;

    public readonly ?string $since;

    public function __construct(?string $message = null, ?string $since = null)
    {
        $this->message = $message;
        $this->since = $since;
    }
}