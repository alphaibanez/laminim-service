<?php

namespace Lkt\Factory\Schemas\Fields;

class ValueListField extends StringField
{
    const TYPE = 'value-list';

    protected string $separator = ';';
    protected string $readMode = 'both';

    public function setSeparator(string $separator = ';'): static
    {
        $this->separator = $separator;
        return $this;
    }

    public function getSeparator(): string
    {
        return $this->separator;
    }

    public function setReadModeToString(): static
    {
        $this->readMode = 'string';
        return $this;
    }

    public function setReadModeToArray(): static
    {
        $this->readMode = 'array';
        return $this;
    }

    public function readModeIsBoth(): bool
    {
        return $this->readMode === 'both';
    }

    public function readModeIsString(): bool
    {
        return $this->readMode === 'string';
    }

    public function readModeIsArray(): bool
    {
        return $this->readMode === 'array';
    }
}