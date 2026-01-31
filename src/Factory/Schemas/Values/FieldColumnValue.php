<?php

namespace Lkt\Factory\Schemas\Values;

use Lkt\Factory\Schemas\Exceptions\InvalidFieldNameException;

final class FieldColumnValue
{
    private string $value;

    /**
     * @param string $value
     * @param string $name
     * @throws InvalidFieldNameException
     */
    public function __construct(string $value, string $name = '')
    {
        if (!$value) {
            $value = $name;
        }

        if (!$value) {
            throw new InvalidFieldNameException();
        }
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}