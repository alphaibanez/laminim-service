<?php

namespace Lkt\Factory\Schemas\Traits;

use Lkt\Factory\Schemas\Exceptions\InvalidComponentException;
use Lkt\Factory\Schemas\Values\ComponentValue;

trait FieldWithComponentOptionTrait
{
    protected ?ComponentValue $component = null;

    protected bool $autoRemoveUnlinked = false;

    /**
     * @param string $component
     * @return $this
     * @throws InvalidComponentException
     */
    final public function setComponent(string $component = ''): static
    {
        $this->component = new ComponentValue($component);
        return $this;
    }

    final public function getComponent(): string
    {
        if ($this->component instanceof ComponentValue) {
            return $this->component->getValue();
        }
        return '';
    }

    public function setAutoRemoveUnlinked(bool $enabled = true): static
    {
        $this->autoRemoveUnlinked = $enabled;
        return $this;
    }

    public function hasToAutoRemoveUnlinked(): bool
    {
        return $this->autoRemoveUnlinked;
    }
}