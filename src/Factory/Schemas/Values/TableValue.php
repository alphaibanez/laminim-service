<?php

namespace Lkt\Factory\Schemas\Values;

use Lkt\Factory\Schemas\Exceptions\InvalidTableException;

final class TableValue
{
    private string $value;

    /**
     * @param string $value
     * @throws InvalidTableException
     */
    public function __construct(string $value)
    {
        if (!$value) {
            throw new InvalidTableException();
        }
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}