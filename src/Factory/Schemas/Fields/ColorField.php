<?php

namespace Lkt\Factory\Schemas\Fields;

use Lkt\Factory\Fields\Interfaces\Field;
use Lkt\Factory\Fields\Traits\BaseFieldTrait;
use Lkt\Factory\Fields\Traits\FieldWithDefaultValue;
use Lkt\Factory\Fields\Traits\FieldWithEmptyDataModeTrait;
use Lkt\Factory\Fields\Traits\FieldWithInvalidDataModeTrait;
use Lkt\Factory\Fields\Traits\FieldWithNullOptionTrait;

class ColorField implements Field
{
    use BaseFieldTrait,
        FieldWithDefaultValue,
        FieldWithNullOptionTrait,
        FieldWithEmptyDataModeTrait,
        FieldWithInvalidDataModeTrait;
}