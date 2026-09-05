<?php

namespace Lkt\Factory\Fields\Traits;

trait FieldWithDefaultValue
{
    protected array $defaultValue = [];
    protected array $onEqualOverrideWithDefaultValue = [];

    public function setDefaultValue($value): static
    {
        $this->defaultValue[0] = $value;
        return $this;
    }

    public function hasDefaultValue(): bool
    {
        if (property_exists($this, 'defaultCurrentTimestamp') && $this->defaultCurrentTimestamp === true) {
            return true;
        }
        return isset($this->defaultValue[0]);
    }

    public function getDefaultValue(): mixed
    {
        if (property_exists($this, 'defaultCurrentTimestamp') && $this->defaultCurrentTimestamp === true) {
            return new \DateTime();
        }
        if (is_callable($this->defaultValue[0])) {
            return call_user_func($this->defaultValue[0]);
        }

        return $this->defaultValue[0];
    }

    public function getOnInstanceUpdateValue(): mixed
    {
        if (property_exists($this, 'onUpdateCurrentTimestamp') && $this->onUpdateCurrentTimestamp === true) {
            return new \DateTime();
        }

        if (is_callable($this->defaultValue[0])) {
            return call_user_func($this->defaultValue[0]);
        }

        return $this->defaultValue[0];
    }

    public function overrideWithDefaultValueIfEqualTo($value): static
    {
        $this->onEqualOverrideWithDefaultValue[] = $value;
        return $this;
    }

    public function ensureDefaultValue($value): mixed
    {
        if (in_array($value, $this->onEqualOverrideWithDefaultValue, true)) {
            return $this->defaultValue[0];
        }

        return $value;
    }
}