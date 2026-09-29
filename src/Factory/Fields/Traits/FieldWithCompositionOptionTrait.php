<?php

namespace Lkt\Factory\Fields\Traits;

use Lkt\Factory\Fields\Enums\ComposedValueFeedType;

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

    public function setCompositionValue(string $paramName, mixed $extractParamValueFromFieldName, ComposedValueFeedType $type = ComposedValueFeedType::ExtractValue): static
    {
        $this->compositionValues[$paramName] = [$extractParamValueFromFieldName, $type];
        return $this;
    }

    public function getCompositionValues(): array
    {
        return $this->compositionValues;
    }
}