<?php

namespace Lkt\Factory\Schemas\Traits;

use Lkt\Factory\Schemas\Exceptions\InvalidFieldNameException;

trait FieldWithMultipleOptionTrait
{
    protected bool|null $allowMultiple = null;

    final public function setMultiple(bool $allow = true): self
    {
        $this->allowMultiple = $allow;
        return $this;
    }

    final public function isMultiple(): bool
    {
        return $this->allowMultiple === true;
    }

    /**
     * @throws InvalidFieldNameException
     */
    final public static function defineMultiple(string $name, string $column = ''): static
    {
        return (new static($name, $column))->setMultiple(true);
    }
}