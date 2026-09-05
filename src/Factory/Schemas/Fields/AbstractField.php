<?php

namespace Lkt\Factory\Schemas\Fields;

use Lkt\Factory\Fields\Traits\BaseFieldTrait;
use Lkt\Factory\Fields\Traits\FieldWithDefaultValue;
use Lkt\Factory\Fields\Traits\NonRelationalFieldInstantiation;

abstract class AbstractField
{
    use BaseFieldTrait,
        FieldWithDefaultValue;

    use NonRelationalFieldInstantiation;

    /**
     * @deprecated
     * @return string
     */
    public function getGetterForComputed(): string
    {
        if ($this instanceof BooleanField) {
            return $this->getName();
        }
        return 'get'. ucfirst($this->getName());
    }
}