<?php

namespace Lkt\Factory\Schemas\Fields;

use Lkt\Attributes\Deprecated;
use Lkt\Factory\Fields\Enums\StringFieldType;
use Lkt\Factory\Fields\Interfaces\NonRelationalField;

/** @deprecated Use StringField::email instead */
#[Deprecated]
class EmailField extends StringField implements NonRelationalField
{
    public static function define(string $name, string $column = ''): static
    {
        $ins = parent::define($name, $column);
        $ins->fieldType = StringFieldType::Email;
        return $ins;
    }
}