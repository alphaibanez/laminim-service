<?php

namespace Lkt\Factory\Schemas\Fields;

use Lkt\Factory\Fields\Enums\FileFieldType;

/**
 * @deprecated use FileField::image instead
 */
class ImageField extends FileField
{
    public static function define(string $name, string $column = ''): static
    {
        $ins = parent::define($name, $column);
        $ins->fieldType = FileFieldType::Image;
        return $ins;
    }
}