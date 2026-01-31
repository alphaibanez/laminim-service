<?php

namespace Lkt\Factory\Schemas\Fields;

use Lkt\Factory\Schemas\Traits\FieldWithNullOptionTrait;

class HTMLField extends AbstractField
{
    const TYPE = 'html';

    use FieldWithNullOptionTrait;
}