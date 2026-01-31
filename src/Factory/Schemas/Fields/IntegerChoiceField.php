<?php

namespace Lkt\Factory\Schemas\Fields;

use Lkt\Factory\Schemas\Traits\FieldWithChoiceOptionTrait;

class IntegerChoiceField extends IntegerField
{
    const TYPE = 'integer-choice';

    use FieldWithChoiceOptionTrait;
}