<?php

namespace Lkt\Factory\Schemas\Fields;

use Lkt\Factory\Fields\Traits\BaseFieldTrait;
use Lkt\Factory\Fields\Traits\FieldWithDefaultValue;

/**
 * @deprecated
 */
abstract class AbstractField
{
    use BaseFieldTrait,
        FieldWithDefaultValue;
}