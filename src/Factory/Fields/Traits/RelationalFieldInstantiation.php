<?php

namespace Lkt\Factory\Fields\Traits;

use Lkt\Factory\Schemas\Exceptions\InvalidFieldNameException;

trait RelationalFieldInstantiation
{
    /**
     * @throws InvalidFieldNameException
     */
    public function __construct(string $component, string $name, string|null $column = null)
    {
        if (!$name) throw new InvalidFieldNameException();

        $this->component = $component;
        $this->name = $name;
        if (!$column) $column = $name;
        if (!$column) throw new InvalidFieldNameException();

        $this->column = $column;
    }

    /**
     * @throws InvalidFieldNameException
     */
    final public static function define(string $component, string $name, string $column = ''): static
    {
        return new static($component, $name, $column);
    }

    /**
     * @deprecated
     * @param string $component
     * @param string $name
     * @param string $column
     * @return static
     * @throws InvalidFieldNameException
     */
    public static function defineRelation(string $component, string $name, string $column = ''): static
    {
        return (new static($name, $column))->setComponent($component);
    }


    public static function identifier(string $component, string $name, string|null $column = null): static
    {
        $r = new static($component, $name, $column);
        $r->setIsIdentifier(true);
        return $r;
    }
}