<?php

namespace Lkt\Factory\Instantiator\Instances\AccessDataTraits;

trait ColumnEmailTrait
{
    protected function _getEmailVal(string $fieldName): string
    {
        return $this->stringData->get($fieldName);
    }

    protected function _hasEmailVal(string $fieldName): bool
    {
        return $this->stringData->has($fieldName);
    }

    protected function _setEmailVal(string $fieldName, string $value = null): static
    {
        $this->stringData->set($fieldName, $value);
        return $this;
    }
}