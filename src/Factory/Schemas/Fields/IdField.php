<?php

namespace Lkt\Factory\Schemas\Fields;

use Lkt\Attributes\Deprecated;

/**
 * @deprecated
 * Use IntegerField::identifier insted
 */
#[Deprecated]
class IdField extends IntegerField
{
    protected bool $isIdentifier = true;

    public static function define(string $name, string $column = ''): static
    {
        return parent::identifier($name, $column);
    }
}