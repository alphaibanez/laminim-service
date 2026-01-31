<?php

namespace Lkt\Factory\Schemas\Traits;

use Lkt\Factory\Schemas\Exceptions\InvalidComponentException;
use Lkt\Factory\Schemas\Values\ComponentValue;

trait FieldWithPivotOptionTrait
{
    protected ?ComponentValue $pivotComponent = null;

    /**
     * @throws InvalidComponentException
     */
    final public function setPivotComponent(string $component = ''): static
    {
        $this->pivotComponent = new ComponentValue($component);
        return $this;
    }
}