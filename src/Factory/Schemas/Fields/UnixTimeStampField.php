<?php

namespace Lkt\Factory\Schemas\Fields;

use Lkt\Attributes\Deprecated;

/** @deprecated Use DateTimeField::unixTimeStamp instead */
#[Deprecated]
class UnixTimeStampField extends DateTimeField
{
    public static function define(string $name, string $column = ''): static
    {
        return parent::unixTimeStamp($name, $column);
    }
}