<?php

namespace Lkt\Factory\Schemas\Fields;

/**
 * @deprecated
 * Use IntegerField::identifier insted
 */
class IdField extends IntegerField
{
    protected bool $isIdentifier = true;

    public static function define(string $name, string $column = ''): static
    {
        return parent::identifier($name, $column);
    }
}