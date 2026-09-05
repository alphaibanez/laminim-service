<?php

namespace Lkt\Factory\Schemas\Fields;

/**
 * @deprecated use JSON::associative insted
 */
class AssocJSONField extends JSONField
{
    public function __construct(string $name, string $column = '')
    {
        parent::__construct($name, $column);
        $this->setIsAssoc();
    }
}