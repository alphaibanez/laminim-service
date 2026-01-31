<?php

namespace Lkt\Factory\Schemas\Fields;

use Lkt\Factory\Schemas\Traits\FieldWithNullOptionTrait;

class BooleanField extends AbstractField
{
    const TYPE = 'boolean';

    use FieldWithNullOptionTrait;
}