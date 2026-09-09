<?php

namespace Lkt\Factory\Schemas\Fields;

use Lkt\Factory\Fields\Interfaces\NonRelationalField;
use Lkt\Factory\Fields\Traits\FieldWithEmptyDataModeTrait;
use Lkt\Factory\Fields\Traits\FieldWithInvalidDataModeTrait;
use Lkt\Factory\Fields\Traits\FieldWithNullOptionTrait;

class BooleanField extends AbstractField implements NonRelationalField
{
    use FieldWithNullOptionTrait,
        FieldWithEmptyDataModeTrait,
        FieldWithInvalidDataModeTrait;
}