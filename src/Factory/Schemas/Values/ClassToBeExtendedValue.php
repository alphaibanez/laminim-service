<?php

namespace Lkt\Factory\Schemas\Values;

use Lkt\Factory\Schemas\Exceptions\InvalidSchemaAppClassException;

final class ClassToBeExtendedValue
{
    private string $value;

    /**
     * @param string $value
     * @param string $name
     * @throws InvalidSchemaAppClassException
     */
    public function __construct(string $value, string $name = '')
    {
        if (!$value) {
            $value = $name;
        }

        if (!$value) {
            throw new InvalidSchemaAppClassException();
        }
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}