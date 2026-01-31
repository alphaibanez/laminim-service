<?php

namespace Lkt\Factory\Schemas\Fields;

use Lkt\Factory\Schemas\Traits\FieldWithMultipleOptionTrait;
use Lkt\Factory\Schemas\Traits\FieldWithNullOptionTrait;

class IntegerField extends AbstractField
{
    const TYPE = 'integer';

    use FieldWithNullOptionTrait,
        FieldWithMultipleOptionTrait;
}