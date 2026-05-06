<?php

namespace Lkt\Factory\Schemas\Traits;

use Lkt\Factory\Instantiator\ComponentId;
use Lkt\Factory\Instantiator\Instances\AbstractInstance;
use Lkt\Factory\Schemas\Exceptions\InvalidComponentException;
use Lkt\Factory\Schemas\Schema;
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

    final public function getComponent(Schema|null $schema = null, AbstractInstance|null $instance = null): string
    {
        if ($schema && $instance && method_exists($this, 'getDynamicComponentField')) {
            $dynamicComponentFieldName = $this->getDynamicComponentField();
            if ($dynamicComponentFieldName !== '') {
                $dynamicComponentField = $schema->getField($dynamicComponentFieldName);
                $getter = $dynamicComponentField->getGetterForPrimitiveValue();
                $dynamicType = $instance->{$getter}();
                if (is_numeric($dynamicType)) return ComponentId::getComponent((int)$dynamicType);
                elseif ($dynamicType !== '') return $dynamicType;
            }
        }

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