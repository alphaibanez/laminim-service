<?php

namespace Lkt\Factory\Instantiator\Instances\AccessDataTraits;

trait ColumnConcatTrait
{
    protected function _getConcatVal(string $fieldName): string
    {
        return $this->concatData->get($fieldName);
    }

    protected function _hasConcatVal(string $fieldName): bool
    {
        return $this->concatData->has($fieldName);
    }
}