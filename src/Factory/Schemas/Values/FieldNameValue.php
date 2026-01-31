<?php

namespace Lkt\Factory\Schemas\Values;

use Lkt\Factory\Schemas\Exceptions\InvalidFieldNameException;

final class FieldNameValue
{
    private string $value;

    /**
     * @param string $value
     * @throws InvalidFieldNameException
     */
    public function __construct(string $value)
    {
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