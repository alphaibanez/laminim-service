<?php

namespace Lkt\Factory\Schemas\Fields;

use Lkt\Factory\Schemas\Traits\FieldWithChoiceOptionTrait;
use Lkt\Factory\Schemas\Traits\FieldWithPrefabRoleTrait;

class IntegerChoiceField extends IntegerField
{
    const TYPE = 'integer-choice';

    use FieldWithChoiceOptionTrait,
        FieldWithPrefabRoleTrait;
}