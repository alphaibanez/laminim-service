<?php

namespace Lkt\Factory\Schemas\Fields;

use Lkt\Factory\Schemas\Traits\FieldWithNullOptionTrait;

class ConstantValueField extends AbstractField
{
    const TYPE = 'constant-value';

    use FieldWithNullOptionTrait;

    protected string $constantValueType = 'string';
    protected mixed $constantValue;

    public function getConstantValue():mixed
    {
        return $this->constantValue;
    }

    public function getConstantValueType():mixed
    {
        return $this->constantValueType;
    }

    public static function defineString(string $name, string $value): static
    {
        $r = new static($name);
        $r->constantValue = $value;
        return $r;
    }

    public static function defineInteger(string $name, int $value): static
    {
        $r = new static($name);
        $r->constantValue = $value;
        $r->constantValueType = 'int';
        return $r;
    }

    public static function defineFloat(string $name, float $value): static
    {
        $r = new static($name);
        $r->constantValue = $value;
        $r->constantValueType = 'float';
        return $r;
    }

    public static function defineBoolean(string $name, bool $value): static
    {
        $r = new static($name);
        $r->constantValue = $value;
        $r->constantValueType = 'bool';
        return $r;
    }
}