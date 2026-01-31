<?php

namespace Lkt\Factory\Schemas\Fields;

use Lkt\Factory\Schemas\Traits\FieldWithNullOptionTrait;

class ColorField extends AbstractField
{
    const TYPE = 'color';

    use FieldWithNullOptionTrait;
}