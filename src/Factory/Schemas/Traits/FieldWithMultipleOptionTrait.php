<?php

namespace Lkt\Factory\Schemas\Traits;

use Lkt\Factory\Schemas\Exceptions\InvalidFieldNameException;
use Lkt\Factory\Schemas\Values\BooleanValue;

trait FieldWithMultipleOptionTrait
{
    protected ?BooleanValue $allowMultiple = null;

    final public function setMultiple(bool $allow = true): self
    {
        $this->allowMultiple = new BooleanValue($allow);
        return $this;
    }

    final public function isMultiple(): bool
    {
        if ($this->allowMultiple instanceof BooleanValue) {
            return $this->allowMultiple->getValue();
        }
        return false;
    }

    /**
     * @throws InvalidFieldNameException
     */
    final public static function defineMultiple(string $name, string $column = ''): static
    {
        return (new static($name, $column))->setMultiple(true);
    }
}