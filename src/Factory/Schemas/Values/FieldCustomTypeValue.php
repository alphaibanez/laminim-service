<?php

namespace Lkt\Factory\Schemas\Values;

use Lkt\Factory\Schemas\Exceptions\InvalidFieldNameException;

final class FieldCustomTypeValue
{
    private string $value;
    public function __construct(string $value = '')
    {
        if (!$value) $value = '';
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}