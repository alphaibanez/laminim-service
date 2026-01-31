<?php

namespace Lkt\Factory\Schemas\Values;

use Lkt\Factory\Schemas\Exceptions\InvalidComponentException;

final class ComponentValue
{
    private string $value;

    /**
     * @param string $value
     * @throws InvalidComponentException
     */
    public function __construct(string $value)
    {
        if (!$value) {
            throw new InvalidComponentException();
        }
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}