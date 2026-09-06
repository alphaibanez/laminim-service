<?php

namespace Lkt\Factory\Schemas\Fields;

/** @deprecated Use DateTimeField::unixTimeStamp instead */
class UnixTimeStampField extends DateTimeField
{
    public static function define(string $name, string $column = ''): static
    {
        return parent::unixTimeStamp($name, $column);
    }
}