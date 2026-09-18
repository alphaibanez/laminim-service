<?php

namespace Lkt\Factory\Schemas\ComputedFields;

use Lkt\Factory\Fields\Interfaces\Field;
use Lkt\Factory\Fields\Traits\BaseFieldTrait;
use Lkt\Factory\Fields\Traits\FieldWithDefaultValue;

abstract class AbstractComputedField implements Field
{
    use BaseFieldTrait,
        FieldWithDefaultValue;

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