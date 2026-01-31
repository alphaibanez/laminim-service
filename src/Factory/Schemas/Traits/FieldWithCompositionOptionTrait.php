<?php

namespace Lkt\Factory\Schemas\Traits;

trait FieldWithCompositionOptionTrait
{

    protected array $compositionContent = [];
    protected array $compositionValues = [];

    public function setCompositionContent(array $fields): static
    {
        $this->compositionContent = $fields;
        return $this;
    }

    public function getCompositionContent(): array
    {
        return $this->compositionContent;
    }

    public function hasCompositionContent(): bool
    {
        return count($this->compositionContent) > 0;
    }

    public function setCompositionValue(string $paramName, string $extractParamValueFromFieldName): static
    {
        $this->compositionValues[$paramName] = $extractParamValueFromFieldName;
        return $this;
    }

    public function getCompositionValues(): array
    {
        return $this->compositionValues;
    }
}