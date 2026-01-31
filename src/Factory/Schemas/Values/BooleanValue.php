<?php

namespace Lkt\Factory\Schemas\Values;

final class BooleanValue
{
    private bool $value = false;

    public function __construct(bool $value)
    {
        if (!$value) {
            $value = false;
        }
        $this->value = $value === true;
    }

    public function getValue(): bool
    {
        return $this->value;
    }
}