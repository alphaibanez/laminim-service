<?php

namespace Lkt\Factory\Schemas\Fields;

use Lkt\Factory\Schemas\Traits\FieldWithChoiceOptionTrait;
use Lkt\Factory\Schemas\Traits\FieldWithPrefabRoleTrait;

class IntegerChoiceField extends IntegerField
{
    use FieldWithChoiceOptionTrait,
        FieldWithPrefabRoleTrait;
}