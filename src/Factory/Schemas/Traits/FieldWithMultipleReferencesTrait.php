<?php

namespace Lkt\Factory\Schemas\Traits;

trait FieldWithMultipleReferencesTrait
{
    protected array $multipleReferences = [];

    public static function defineWithMultipleReferences(string $name, array $columns = []): self
    {
        return (new static($name, implode(';', $columns)))->setMultipleReferences($columns);
    }

    protected function setMultipleReferences(array $columns): self
    {
        $this->multipleReferences = $columns;
        return $this;
    }

    public function getMultipleReferences(): array
    {
        return $this->multipleReferences;
    }

    public function hasMultipleReferences(): bool
    {
        return count($this->multipleReferences) > 0;
    }
}