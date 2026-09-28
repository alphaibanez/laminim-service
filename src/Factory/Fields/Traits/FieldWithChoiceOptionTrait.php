<?php

namespace Lkt\Factory\Fields\Traits;

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
    protected array $finalChoices = [];

    /**
     * @laminim
     * Expected structure:
     *      An key-value array where 'key' indicates current value
     *      and 'value' it's an array containing all posible values
     *
     *      For example:
     *      [
     *          'open' => ['closed', 'not-defined'],
     *          'closed' => ['open'],
     *          'not-defined' => ['open'],
     *      ]
     *
     *      In this case, if the value it's open, it can be turned into closed or not-defined,
     *      but both closed and not-defined can only move to open value.
     *
     *      Keep in mind if the initial value it's a final choice, it won't be changed.
     */
    protected array $choiceFlow = [];

    protected ChoiceFieldSource $optionsSource = ChoiceFieldSource::None;

    /**
     * @deprecated
     */
    final public function setAllowedOptions(array $options): static
    {
        $this->allowedOptions = $options;
        return $this;
    }

    /**
     * @deprecated
     */
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
        $ins = new static($name, $column);
        $ins->allowedOptions = $options;
        $ins->optionsSource = ChoiceFieldSource::Array;
        return $ins;
    }

    final public static function enumChoice(string $enumChoiceClass, string $name, string $column = ''): static
    {
        $ins = new static($name, $column);
        $ins->enumChoiceClass = $enumChoiceClass;
        $ins->allowedOptions = enumToArray($enumChoiceClass);
        $ins->optionsSource = ChoiceFieldSource::Enum;
        return $ins;
    }

    public function ableToChoose(): bool
    {
        return $this->optionsSource !== ChoiceFieldSource::None;
    }

    final public function isEnumChoice(): bool
    {
        return $this->optionsSource === ChoiceFieldSource::Enum;
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

    final public function setFinalChoices(array $choices): static
    {
        $this->finalChoices = $choices;
        return $this;
    }

    final public function getFinalChoices(): array
    {
        return $this->finalChoices;
    }

    final public function setChoiceFlow(array $choices): static
    {
        $this->choiceFlow = $choices;
        return $this;
    }

    final public function getChoiceFlow(): array
    {
        return $this->choiceFlow;
    }
}