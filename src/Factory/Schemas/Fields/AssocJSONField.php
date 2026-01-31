<?php

namespace Lkt\Factory\Schemas\Fields;

class AssocJSONField extends JSONField
{
    public function __construct(string $name, string $column = '')
    {
        parent::__construct($name, $column);
        $this->setIsAssoc();
    }
}