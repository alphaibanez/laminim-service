<?php

namespace Lkt\Factory\Schemas\Fields;

use Lkt\Factory\Fields\Interfaces\NonRelationalField;
use Lkt\Factory\Fields\Traits\BaseFieldTrait;
use Lkt\Factory\Fields\Traits\FieldWithDefaultValue;
use Lkt\Factory\Fields\Traits\FieldWithTrimMode;
use Lkt\Factory\Fields\Traits\FieldWithUniqueValue;
use Lkt\Factory\Fields\Traits\NonRelationalFieldInstantiation;
use Lkt\Factory\Schemas\Traits\FieldWithEmptyDataModeTrait;
use Lkt\Factory\Schemas\Traits\FieldWithInvalidDataModeTrait;
use Lkt\Factory\Schemas\Traits\FieldWithJsonI18nStorageTrait;
use Lkt\Factory\Schemas\Traits\FieldWithMandatoryOptionTrait;
use Lkt\Factory\Schemas\Traits\FieldWithNullOptionTrait;

class StringField extends AbstractField implements NonRelationalField
{
//    use BaseFieldTrait,
//        FieldWithDefaultValue,
//        NonRelationalFieldInstantiation;

    use FieldWithNullOptionTrait,
        FieldWithJsonI18nStorageTrait,
        FieldWithMandatoryOptionTrait,
        FieldWithInvalidDataModeTrait,
        FieldWithEmptyDataModeTrait,
        FieldWithUniqueValue,
        FieldWithTrimMode;

    public static function i18n(string $name, string $column = ''): static
    {
        $ins = new static($name, $column);
        $ins->storeAsI18nJson = true;
        return $ins;
    }
}