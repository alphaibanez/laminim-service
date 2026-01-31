<?php

namespace Lkt\Factory\Schemas\Values;

final class FieldOrderValue
{
    private array $value = [];

    /**
     * @param $value
     */
    public function __construct($value)
    {
        if (!$value) {
            $value = [];
        }

        if (!is_array($value)) {
            $value = [$value];
        }
        $this->value = $value;
    }

    public function getValue(): array
    {
        return $this->value;
    }
}