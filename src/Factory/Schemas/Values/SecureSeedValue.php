<?php

namespace Lkt\Factory\Schemas\Values;

use Lkt\Factory\Schemas\Exceptions\InvalidSecureSeedException;

final class SecureSeedValue
{
    private string $value;

    /**
     * @throws InvalidSecureSeedException
     */
    public function __construct(string $value)
    {
        if (!$value) {
            throw new InvalidSecureSeedException();
        }
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}