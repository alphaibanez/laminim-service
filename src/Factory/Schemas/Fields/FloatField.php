<?php

namespace Lkt\Factory\Schemas\Fields;

use Lkt\Factory\Schemas\Traits\FieldWithNullOptionTrait;

class FloatField extends AbstractField
{
    const TYPE = 'float';

    use FieldWithNullOptionTrait;
}