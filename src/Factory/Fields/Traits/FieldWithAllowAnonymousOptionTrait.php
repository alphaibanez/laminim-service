<?php

namespace Lkt\Factory\Fields\Traits;

trait FieldWithAllowAnonymousOptionTrait
{
    protected bool|null $allowAnonymous = null;

    final public function setAllowAnonymous(bool $allow = true): static
    {
        $this->allowAnonymous = $allow;
        return $this;
    }

    final public function anonymousAllowed(): bool
    {
        return $this->allowAnonymous === true;
    }
}