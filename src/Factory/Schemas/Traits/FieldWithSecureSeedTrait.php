<?php

namespace Lkt\Factory\Schemas\Traits;

use Lkt\Factory\Schemas\Values\SecureSeedValue;

trait FieldWithSecureSeedTrait
{
    protected ?SecureSeedValue $secureSeed = null;

    public function setSecureSeed(string $secureSeed): static
    {
        $this->secureSeed = new SecureSeedValue($secureSeed);
        return $this;
    }

    public function getSecureSeed(): string
    {
        return $this->secureSeed->getValue();
    }

}