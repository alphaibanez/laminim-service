<?php

namespace Lkt\Factory\Schemas\Fields;

use Lkt\Factory\Fields\Interfaces\NonRelationalField;
use Lkt\Factory\Schemas\Traits\FieldWithEmptyDataModeTrait;
use Lkt\Factory\Schemas\Traits\FieldWithInvalidDataModeTrait;
use Lkt\Factory\Schemas\Traits\FieldWithNullOptionTrait;

class HTMLField extends AbstractField implements NonRelationalField
{
    use FieldWithNullOptionTrait,
        FieldWithEmptyDataModeTrait,
        FieldWithInvalidDataModeTrait;
}