<?php

namespace Lkt\Factory\Schemas\Values;

final class FieldFilePathValue
{
    private mixed $value;

    /**
     * @param string|callable $value
     */
    public function __construct(string|callable $value)
    {
        if (!$value) $value = '';
        $this->value = $value;
    }

    public function getValue($instance = null): string
    {
        if (is_callable($this->value)) return call_user_func($this->value, $instance);
        return $this->value;
    }
}