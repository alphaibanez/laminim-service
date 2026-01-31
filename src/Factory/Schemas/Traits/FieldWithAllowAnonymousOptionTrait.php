<?php

namespace Lkt\Factory\Schemas\Traits;

use Lkt\Factory\Schemas\Values\BooleanValue;

trait FieldWithAllowAnonymousOptionTrait
{
    protected ?BooleanValue $allowAnonymous = null;

    /**
     * @return FieldWithAllowAnonymousOptionTrait
     */
    final public function setAllowAnonymous(bool $allow = true): self
    {
        $this->allowAnonymous = new BooleanValue($allow);
        return $this;
    }

    /**
     * @return bool
     */
    final public function anonymousAllowed(): bool
    {
        if ($this->allowAnonymous instanceof BooleanValue) {
            return $this->allowAnonymous->getValue();
        }
        return true;
    }
}