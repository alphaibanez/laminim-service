<?php

namespace Lkt\Connectors\Curl;

class CurlResponse
{
    public function __construct(
        readonly mixed $result,
        readonly array $info,
    )
    {
    }
}