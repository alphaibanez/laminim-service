<?php

namespace Lkt\Factory\Schemas\Fields;

use Lkt\Factory\Schemas\Traits\FieldWithChoiceOptionTrait;

class StringChoiceField extends StringField
{
    const TYPE = 'string-choice';

    use FieldWithChoiceOptionTrait;
}