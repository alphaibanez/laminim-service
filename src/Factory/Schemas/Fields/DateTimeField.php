<?php

namespace Lkt\Factory\Schemas\Fields;

use Lkt\Factory\Schemas\Traits\DateFieldWithDefaultValueTrait;
use Lkt\Factory\Schemas\Traits\DateFieldWithFormattedValueTrait;
use Lkt\Factory\Schemas\Traits\FieldWithFormatsOptionTrait;
use Lkt\Factory\Schemas\Traits\FieldWithNullOptionTrait;

class DateTimeField extends AbstractField
{
    const TYPE = 'date';

    use FieldWithNullOptionTrait,
        FieldWithFormatsOptionTrait,
        DateFieldWithFormattedValueTrait,
        DateFieldWithDefaultValueTrait;
}