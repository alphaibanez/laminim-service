<?php

namespace Lkt\Factory\Fields\Traits;

use Lkt\Factory\Schemas\Exceptions\InvalidFieldNameException;

trait NonRelationalFieldInstantiation
{
    /**
     * @throws InvalidFieldNameException
     */
    public function __construct(string $name, string|null $column = null)
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
    final public static function define(string $name, string $column = ''): static
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