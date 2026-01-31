<?php

namespace Lkt\Factory\Schemas\Values;

use Lkt\Factory\Schemas\Exceptions\InvalidSchemaClassNameForGeneratedClassException;

final class SchemaClassForGeneratedClassValue
{
    private string $value;

    /**
     * @param string $value
     * @param string $name
     * @throws InvalidSchemaClassNameForGeneratedClassException
     */
    public function __construct(string $value, string $name = '')
    {
        if (!$value) {
            $value = $name;
        }

        if (!$value) {
            throw new InvalidSchemaClassNameForGeneratedClassException();
        }
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}