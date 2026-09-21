<?php

namespace Lkt\Factory\Schemas\Fields;

use Lkt\Factory\Fields\Interfaces\Field;
use Lkt\Factory\Fields\Traits\BaseFieldTrait;
use Lkt\Factory\Fields\Traits\FieldWithDefaultValue;
use Lkt\Factory\Fields\Traits\FieldWithNullOptionTrait;

/**
 * @deprecated
 */
class ConcatField implements Field
{
    use BaseFieldTrait,
        FieldWithDefaultValue,
        FieldWithNullOptionTrait;

    protected array $fields = [];
    protected string $separator = '';


    final public static function concat(string $name, array $fields, string $separator): StringField
    {
        return StringField::concat($name, $fields, $separator);
    }
}