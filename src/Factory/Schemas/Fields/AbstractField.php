<?php

namespace Lkt\Factory\Schemas\Fields;

use Lkt\Factory\Schemas\Exceptions\InvalidFieldNameException;
use Lkt\Factory\Schemas\Traits\BaseFieldTrait;

abstract class AbstractField
{
    use BaseFieldTrait;

    protected array $defaultValue = [];

    protected array $onEqualOverrideWithDefaultValue = [];

    /**
     * @throws InvalidFieldNameException
     */
    public function __construct(string $name, string|null $column = null)
    {
        if (!$name) throw new InvalidFieldNameException();

        $this->name = $name;
        if (!$column) $column = $name;
        if (!$column) throw new InvalidFieldNameException();

        $this->column = $column;
    }

    /**
     * @throws InvalidFieldNameException
     */
    final public static function define(string $name, string $column = ''): static
    {
        return new static($name, $column);
    }

    /**
     * @deprecated Use Item::assignValue instead
     */
    public function getSetterForPrimitiveValue(): string
    {
        if ($this instanceof ForeignKeyField) return 'set'. ucfirst($this->getName()) . 'Id';
        if ($this instanceof ForeignKeysField) return 'set'. ucfirst($this->getName()) . 'Ids';
        return 'set'. ucfirst($this->getName());
    }

    public function getGetterForComputed(): string
    {
        if ($this instanceof BooleanField) {
            return $this->getName();
        }
        return 'get'. ucfirst($this->getName());
    }

    /**
     * @deprecated Use Item::retrieveValue instead
     */
    public function getGetterForPrimitiveValue(): string
    {
        if ($this instanceof BooleanField) return $this->getName();
        if ($this instanceof ForeignKeyField) return 'get'. ucfirst($this->getName()) . 'Id';
        if ($this instanceof ForeignKeysField) return 'get'. ucfirst($this->getName()) . 'Ids';
        if ($this instanceof RelatedKeysField) return 'get'. ucfirst($this->getName()) . 'Ids';
        if ($this instanceof MethodGetterField) return $this->getName();
        return 'get'. ucfirst($this->getName());
    }

    /**
     * @deprecated Use Item::retrieveValue instead
     */
    public function getGetterForData(): string
    {
        if ($this instanceof BooleanField) return $this->getName();
        if ($this instanceof ForeignKeyField) return 'get'. ucfirst($this->getName());
        if ($this instanceof ForeignKeysField) return 'get'. ucfirst($this->getName()) . 'Data';
        if ($this instanceof RelatedKeysField) return 'get'. ucfirst($this->getName());
        if ($this instanceof MethodGetterField) return $this->getName();
        return 'get'. ucfirst($this->getName()) . 'Data';
    }

    /**
     * @deprecated Use Item::hasValue instead
     */
    public function getGetterForChecker(): string
    {
        if ($this instanceof BooleanField) return $this->getName();
        if ($this instanceof ForeignKeyField) return 'has'. ucfirst($this->getName());
        return 'has'. ucfirst($this->getName());
    }

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
        foreach ($this->onEqualOverrideWithDefaultValue as $v) {
            if ($value === $v) {
                return $this->defaultValue[0];
            }
        }

        return $value;
    }
}