<?php

namespace Lkt\Factory\Schemas\Traits;

trait BaseFieldTrait
{
    protected string $name = '';

    protected string $column;
    protected bool $isIdentifier = false;

    final public function getName(): string
    {
        return $this->name;
    }

    final public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    final public function getColumn(): string
    {
        return $this->column;
    }

    final public function getLocaleColumn(string $locale): string
    {
        return "__loc:{$locale}:{$this->column}";
    }

    final public function setIsIdentifier(bool $status = true): static
    {
        $this->isIdentifier = $status;
        return $this;
    }

    final public function isIdentifier(): bool
    {
        return $this->isIdentifier;
    }

    public static function identifier(string $name, string|null $column = null): static
    {
        $r = new static($name, $column);
        $r->setIsIdentifier(true);
        return $r;
    }
}