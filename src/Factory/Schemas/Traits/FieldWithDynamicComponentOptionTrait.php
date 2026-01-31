<?php

namespace Lkt\Factory\Schemas\Traits;

use Lkt\Factory\Schemas\Exceptions\InvalidComponentException;

trait FieldWithDynamicComponentOptionTrait
{
    protected string $dynamicComponentField = '';

    /**
     * @param string $component
     * @return $this
     * @throws InvalidComponentException
     */
    final public function setDynamicComponentField(string $field = ''): static
    {
        $this->dynamicComponentField = $field;
        return $this;
    }

    final public function getDynamicComponentField(): string
    {
        return $this->dynamicComponentField;
    }
}