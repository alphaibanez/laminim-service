<?php

namespace Lkt\Factory\Instantiator\Instances\AccessDataTraits;

trait ColumnConstantValueTrait
{
    protected function _getConstantValueVal(string $fieldName): mixed
    {
        return $this->constantData->get($fieldName);
    }
}