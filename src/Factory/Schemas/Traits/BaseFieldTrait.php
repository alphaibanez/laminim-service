<?php

namespace Lkt\Factory\Schemas\Traits;

trait BaseFieldTrait
{
    protected string $name = '';

    final public function getName(): string
    {
        return $this->name;
    }

    final public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }
}