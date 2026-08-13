<?php

namespace Lkt\Factory\Schemas\Fields;

use Lkt\Factory\Schemas\Exceptions\InvalidFieldNameException;
use Lkt\Factory\Schemas\Values\FieldColumnValue;
use Lkt\Factory\Schemas\Values\FieldCustomTypeValue;
use Lkt\Factory\Schemas\Values\FieldLabelValue;
use Lkt\Factory\Schemas\Values\FieldNameValue;

abstract class AbstractField
{
    const TYPE = '';

    protected FieldNameValue $name;
    protected FieldColumnValue $column;
    protected FieldLabelValue $label;
    protected FieldCustomTypeValue $customType;

    protected $defaultValue = [];

    protected $onEqualOverrideWithDefaultValue = [];

    protected array $customViewName = [];

    protected bool $isIdentifier = false;

    public function setIsIdentifier(bool $status = true): static
    {
        $this->isIdentifier = $status;
        return $this;
    }

    public function isIdentifier(): bool
    {
        return $this->isIdentifier;
    }

    /**
     * @throws InvalidFieldNameException
     */
    public function __construct(string $name, string $column = '')
    {
        $this->name = new FieldNameValue($name);
        $this->column = new FieldColumnValue($column, $this->name->getValue());
        $this->label = new FieldLabelValue('');
        $this->customType = new FieldCustomTypeValue(static::TYPE);
    }

    final public function getName(): string
    {
        return $this->name->getValue();
    }

    final public function setName(string $name): static
    {
        $this->name = new FieldNameValue($name);
        return $this;
    }

    final public function getColumn(): string
    {
        return $this->column->getValue();
    }

    final public function getLocaleColumn(string $locale): string
    {
        return "__loc:{$locale}:{$this->column->getValue()}";
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
    public function getSetter(): string
    {
        return 'set'. ucfirst($this->getName());
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

    /**
     * @deprecated
     */
    public function setLabel(string $label): static
    {
        $this->label = new FieldLabelValue($label);
        return $this;
    }

    /**
     * @deprecated
     */
    public function getLabel(): string
    {
        return $this->label->getValue();
    }

    /**
     * @deprecated
     */
    public function setCustomType(string $type): static
    {
        $this->customType = new FieldCustomTypeValue($type);
        return $this;
    }

    /**
     * @deprecated
     */
    public function getCustomType(): string
    {
        return $this->customType->getValue();
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

    /**
     * @param string $view
     * @param string $name
     * @return $this
     * @deprecated
     */
    public function setCustomViewName(string $view, string $name): static
    {
        $this->customViewName[$view] = $name;
        return $this;
    }

    /**
     * @param string $view
     * @return string
     * @deprecated
     */
    public function getCustomViewName(string $view): string
    {
        if ($this->customViewName[$view] && $this->customViewName[$view] !== '') return $this->customViewName[$view];
        if ($this instanceof MethodGetterField) return $this->getColumn();
        return $this->getName();
    }
}