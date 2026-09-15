<?php

namespace Lkt\Factory\Schemas\Fields;

use Lkt\Attributes\Deprecated;

/**
 * @deprecated use JSON::associative insted
 */
#[Deprecated]
class AssocJSONField extends JSONField
{
    public function __construct(string $name, string $column = '')
    {
        parent::__construct($name, $column);
        $this->setIsAssoc();
    }
}