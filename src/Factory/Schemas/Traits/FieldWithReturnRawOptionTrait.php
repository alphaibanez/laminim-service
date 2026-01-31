<?php

namespace Lkt\Factory\Schemas\Traits;

trait FieldWithReturnRawOptionTrait
{
    protected array $returnRawFields = [];

    final public function returnRaw(array $fields): self
    {
        $this->returnRawFields = $fields;
        return $this;
    }

    final public function getRawReturnFields(): array
    {
        return $this->returnRawFields;
    }

    final public function hasRawReturnFields(): bool
    {
        return count($this->returnRawFields) > 0;
    }
}