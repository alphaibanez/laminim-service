<?php

namespace Lkt\Factory\Schemas;

class DynamicDataSourcePayload
{
    public function __construct(
        readonly string $table,
        readonly array $fields = [],
    )
    {
    }
}