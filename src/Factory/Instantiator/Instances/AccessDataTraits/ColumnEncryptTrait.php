<?php

namespace Lkt\Factory\Instantiator\Instances\AccessDataTraits;

use Lkt\Factory\Instance\Traits\ItemWithEncryptDataTrait;

trait ColumnEncryptTrait
{
    use ItemWithEncryptDataTrait;

    protected function _getEncryptVal(string $fieldName): string
    {
        return $this->encryptData->get($fieldName);
    }

    protected function _getDecryptedVal(string $fieldName): string
    {
        return $this->encryptData->decrypt($fieldName);
    }

    protected function _hasEncryptVal(string $fieldName): bool
    {
        return $this->encryptData->has($fieldName);
    }

    protected function _setEncryptVal(string $fieldName, string $value = null): static
    {
        $this->encryptData->set($fieldName, $value);
        return $this;
    }
}