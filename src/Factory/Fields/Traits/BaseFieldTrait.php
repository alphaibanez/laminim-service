<?php

namespace Lkt\Factory\Fields\Traits;

use Lkt\Factory\Schemas\Exceptions\InvalidFieldNameException;

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

    /**
     * @throws InvalidFieldNameException
     */
    protected function __construct(string $name, string|null $column = null)
    {
        if (!$name) throw new InvalidFieldNameException();

        $this->name = $name;
        if (!$column) $column = $name;
        if (!$column) throw new InvalidFieldNameException();

        $this->column = $column;
    }

    /**
     * @throws InvalidFieldNameException
     */
    public static function define(string $name, string $column = ''): static
    {
        return new static($name, $column);
    }

    public static function identifier(string $name, string|null $column = null): static
    {
        $r = new static($name, $column);
        $r->setIsIdentifier(true);
        return $r;
    }
}