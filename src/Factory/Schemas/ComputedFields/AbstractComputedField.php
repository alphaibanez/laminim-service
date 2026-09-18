<?php

namespace Lkt\Factory\Schemas\ComputedFields;

use Lkt\Factory\Fields\Interfaces\Field;
use Lkt\Factory\Schemas\Fields\AbstractField;

abstract class AbstractComputedField extends AbstractField implements Field
{
    protected $value;
    protected string $field = '';

    protected function setField(string $value): static
    {
        $this->field = $value;
        return $this;
    }

    protected function setValue($value = null): static
    {
        $this->value = $value;
        return $this;
    }

    public function getField()
    {
        return $this->field;
    }

    public function getValue()
    {
        return $this->value;
    }
}