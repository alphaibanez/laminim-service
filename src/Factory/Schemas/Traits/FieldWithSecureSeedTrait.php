<?php

namespace Lkt\Factory\Schemas\Traits;

use Lkt\Factory\Schemas\Exceptions\InvalidSecureSeedException;

trait FieldWithSecureSeedTrait
{
    protected string|null $secureSeed = null;

    public function setSecureSeed(string $secureSeed): static
    {
        if (!$secureSeed) throw new InvalidSecureSeedException();
        $this->secureSeed = $secureSeed;
        return $this;
    }

    public function getSecureSeed(): string
    {
        return $this->secureSeed;
    }

}