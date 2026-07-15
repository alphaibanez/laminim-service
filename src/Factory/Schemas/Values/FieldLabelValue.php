<?php

namespace Lkt\Factory\Schemas\Values;

final class FieldLabelValue
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