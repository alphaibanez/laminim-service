<?php

namespace Lkt\Factory\Fields\Traits;

trait FieldWithPivotOptionTrait
{
    protected string|null $pivotComponent = null;

    final public function setPivotComponent(string $component = ''): static
    {
        $this->pivotComponent = $component;
        return $this;
    }
}