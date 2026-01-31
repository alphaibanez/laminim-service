<?php

namespace Lkt\Factory\Schemas\Traits;

use Lkt\Factory\Schemas\Enums\ChoiceFieldSource;
use function Lkt\Tools\Enums\enumToArray;

trait FieldWithChoiceOptionTrait
{
    protected array $allowedOptions = [];

    protected array $compareIn = [];

    protected bool $enabledEmptyPreset = false;
    protected string|int|null $emptyDefault = null;
    protected string $i18nViewOptions = '';
    protected string $enumChoiceClass = '';

    protected ChoiceFieldSource $optionsSource = ChoiceFieldSource::Array;

    final public function setAllowedOptions(array $options): static
    {
        $this->allowedOptions = $options;
        return $this;
    }

    final public function setEnumChoiceClass(string $enumChoiceClass): static
    {
        $this->enumChoiceClass = $enumChoiceClass;
        return $this;
    }

    final public function getEnumChoiceClass(): string
    {
        return $this->enumChoiceClass;
    }

    final public function getAllowedOptions(): array
    {
        return $this->allowedOptions;
    }

    final public static function choice(array $options, string $name, string $column = ''): static
    {
        return (new static($name, $column))->setAllowedOptions($options);
    }

    final public static function enumChoice(string $enumChoiceClass, string $name, string $column = ''): static
    {
        $r = (new static($name, $column))
            ->setEnumChoiceClass($enumChoiceClass)
            ->setAllowedOptions(enumToArray($enumChoiceClass));
        $r->optionsSource = ChoiceFieldSource::Enum;
        return $r;
    }

    final public function addComparatorIn(string $name, array $values): static
    {
        $this->compareIn[$name] = $values;
        return $this;
    }

    final public function getComparatorsIn(): array
    {
        return $this->compareIn;
    }

    final public function setEnabledEmptyPreset($enabled = true): static
    {
        $this->enabledEmptyPreset = $enabled;
        return $this;
    }

    final public function hasEnabledEmptyPreset(): bool
    {
        return $this->enabledEmptyPreset;
    }

    final public function setEmptyDefault(string|int $value): static
    {
        $this->emptyDefault = $value;
        return $this;
    }

    final public function hasEmptyDefault(): bool
    {
        return $this->emptyDefault !== null;
    }

    final public function getEmptyDefault(): int|string|null
    {
        return $this->emptyDefault;
    }

    final public function setI18nViewOptions(string $value): static
    {
        $this->i18nViewOptions = $value;
        return $this;
    }

    final public function hasI18nViewOptions(): bool
    {
        return $this->i18nViewOptions !== '';
    }

    final public function getI18nViewOptions(): string
    {
        return $this->i18nViewOptions;
    }
}