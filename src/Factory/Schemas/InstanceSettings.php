<?php

namespace Lkt\Factory\Schemas;

use Lkt\Factory\Schemas\Exceptions\InvalidComponentException;
use Lkt\Factory\Schemas\Exceptions\InvalidSchemaAppClassException;
use Lkt\Factory\Schemas\Exceptions\InvalidSchemaClassNameForGeneratedClassException;
use Lkt\Factory\Schemas\Exceptions\InvalidSchemaNamespaceForGeneratedClassException;
use Lkt\Factory\Schemas\Values\ComponentValue;
use Lkt\Factory\Schemas\Values\SchemaAppClassValue;
use Lkt\Factory\Schemas\Values\SchemaClassForGeneratedClassValue;
use Lkt\Factory\Schemas\Values\SchemaNamespaceForGeneratedClassValue;
use Lkt\Factory\Schemas\Values\StringValue;

final class InstanceSettings
{
    private ?SchemaAppClassValue $appClass = null;
    private ?SchemaNamespaceForGeneratedClassValue $namespaceForGeneratedClass = null;
    private ?SchemaClassForGeneratedClassValue $classForGeneratedClass = null;
    private ?StringValue $whereStoreGeneratedClass = null;
    private ?StringValue $classToBeExtended = null;
    private ?ComponentValue $baseComponent = null;
    private ?StringValue $queryCallerClassName = null;
    private ?StringValue $whereClassName = null;
    protected array $implementsInterfaces = [];
    protected array $traits = [];


    public function setInterface(string $interface): self
    {
        $this->implementsInterfaces[] = $interface;
        return $this;
    }

    public function setTrait(string $trait): self
    {
        $this->traits[] = $trait;
        return $this;
    }

    public function getImplementedInterfaces(): array
    {
        return $this->implementsInterfaces;
    }

    public function getUsedTraits(): array
    {
        return $this->traits;
    }

    public function getImplementedInterfacesAsString(): string
    {
        if ($this->hasImplementedInterfaces()) {
            return '\\' . implode(',\\', $this->getImplementedInterfaces());
        }
        return '';
    }

    public function getUsedTraitsAsString(): string
    {
        if ($this->hasUsedTraits()) {
            return '\\' . implode(',\\', $this->getUsedTraits());
        }
        return '';
    }

    public function hasImplementedInterfaces(): bool
    {
        return count($this->implementsInterfaces) > 0;
    }

    public function hasUsedTraits(): bool
    {
        return count($this->traits) > 0;
    }

    /**
     * @throws InvalidSchemaAppClassException
     */
    public function __construct(string $appClass)
    {
        $this->appClass = new SchemaAppClassValue($appClass);
    }

    /**
     * @throws InvalidSchemaAppClassException
     */
    public function getAppClass(): string
    {
        if ($this->appClass instanceof SchemaAppClassValue) {
            return $this->appClass->getValue();
        }
        throw new InvalidSchemaAppClassException();
    }

    /**
     * @param string $appClass
     * @return InstanceSettings
     * @throws InvalidSchemaAppClassException
     */
    public static function define(string $appClass): InstanceSettings
    {
        return new InstanceSettings($appClass);
    }

    /**
     * @param string $namespace
     * @return $this
     * @throws InvalidSchemaNamespaceForGeneratedClassException
     */
    public function setNamespaceForGeneratedClass(string $namespace): InstanceSettings
    {
        $this->namespaceForGeneratedClass = new SchemaNamespaceForGeneratedClassValue($namespace);
        return $this;
    }

    /**
     * @return string
     * @throws InvalidSchemaNamespaceForGeneratedClassException
     */
    public function getNamespaceForGeneratedClass(): string
    {
        if ($this->namespaceForGeneratedClass instanceof SchemaNamespaceForGeneratedClassValue) {
            return $this->namespaceForGeneratedClass->getValue();
        }
        throw new InvalidSchemaNamespaceForGeneratedClassException();
    }

    /**
     * @param string $name
     * @return $this
     * @throws InvalidSchemaClassNameForGeneratedClassException
     */
    public function setClassNameForGeneratedClass(string $name): InstanceSettings
    {
        $this->classForGeneratedClass = new SchemaClassForGeneratedClassValue($name);
        return $this;
    }

    /**
     * @return string
     * @throws InvalidSchemaClassNameForGeneratedClassException
     */
    public function getClassNameForGeneratedClass(): string
    {
        if ($this->classForGeneratedClass instanceof SchemaClassForGeneratedClassValue) {
            return $this->classForGeneratedClass->getValue();
        }

        $generatedClassName = $this->getAppClass();
        if ($generatedClassName === '') {
            throw new InvalidSchemaClassNameForGeneratedClassException();
        }
        $generatedClassName = explode('\\', $generatedClassName);
        $generatedClassName = $generatedClassName[count($generatedClassName) - 1];
        return "Generated{$generatedClassName}";
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setWhereStoreGeneratedClass(string $name): InstanceSettings
    {
        $this->whereStoreGeneratedClass = new StringValue($name);
        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setQueryCallerClassName(string $name): InstanceSettings
    {
        $this->queryCallerClassName = new StringValue($name);
        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setWhereClassName(string $name): InstanceSettings
    {
        $this->whereClassName = new StringValue($name);
        return $this;
    }

    /**
     * @return string
     */
    public function getWhereStoreGeneratedClass(): string
    {
        if ($this->whereStoreGeneratedClass instanceof StringValue) {
            return $this->whereStoreGeneratedClass->getValue();
        }
        return '';
    }

    /**
     * @return string
     */
    public function getQueryCallerClassName(): string
    {
        if ($this->queryCallerClassName instanceof StringValue) {
            return $this->queryCallerClassName->getValue();
        }

        $generatedClassName = $this->getAppClass();
        if ($generatedClassName === '') {
            throw new InvalidSchemaClassNameForGeneratedClassException();
        }
        $generatedClassName = explode('\\', $generatedClassName);
        $generatedClassName = $generatedClassName[count($generatedClassName) - 1];
        return "{$generatedClassName}QueryBuilder";
    }

    /**
     * @return string
     */
    public function getWhereClassName(): string
    {
        if ($this->whereClassName instanceof StringValue) {
            return $this->whereClassName->getValue();
        }

        $generatedClassName = $this->getAppClass();
        if ($generatedClassName === '') {
            throw new InvalidSchemaClassNameForGeneratedClassException();
        }
        $generatedClassName = explode('\\', $generatedClassName);
        $generatedClassName = $generatedClassName[count($generatedClassName) - 1];
        return "{$generatedClassName}Where";
    }

    public function getQueryCallerFQDN(): string
    {
        $r = [$this->getNamespaceForGeneratedClass(), $this->getQueryCallerClassName()];
        return implode('\\', $r);
    }

    public function getWhereFQDN(): string
    {
        $r = [$this->getNamespaceForGeneratedClass(), $this->getWhereClassName()];
        return implode('\\', $r);
    }

    public function hasWhereStoreGeneratedClass(): bool
    {
        return $this->getWhereStoreGeneratedClass() !== '';
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setClassToBeExtended(string $name): InstanceSettings
    {
        $this->classToBeExtended = new StringValue($name);
        return $this;
    }

    public function getClassToBeExtended(): string
    {
        if ($this->classToBeExtended instanceof StringValue) {
            return $this->classToBeExtended->getValue();
        }
        return '';
    }

    public function hasLegalExtendClass(): bool
    {
        $class = $this->getClassToBeExtended();
        return $class !== ''
            && class_exists($class)
            && defined("{$class}::COMPONENT");
    }

    public function getGeneratedClassFullPath(): string
    {
        $r = '';
        if ($this->hasWhereStoreGeneratedClass()) {
            $r .= $this->getWhereStoreGeneratedClass() . '/';
        }

        $r .= $this->getClassNameForGeneratedClass() . '.php';
        return $r;
    }

    public function getQueryCallerFullPath(): string
    {
        $r = '';
        if ($this->hasWhereStoreGeneratedClass()) {
            $r .= $this->getWhereStoreGeneratedClass() . '/';
        }

        $r .= $this->getQueryCallerClassName() . '.php';
        return $r;
    }

    public function getWhereFullPath(): string
    {
        $r = '';
        if ($this->hasWhereStoreGeneratedClass()) {
            $r .= $this->getWhereStoreGeneratedClass() . '/';
        }

        $r .= $this->getWhereClassName() . '.php';
        return $r;
    }

    /**
     * @throws InvalidComponentException
     */
    public function setBaseComponent(string $name): InstanceSettings
    {
        $this->baseComponent = new ComponentValue($name);
        return $this;
    }

    /**
     * @return string
     * @throws InvalidComponentException
     */
    public function getBaseComponent(): string
    {
        if ($this->baseComponent instanceof ComponentValue) {
            return $this->baseComponent->getValue();
        }
        throw new InvalidComponentException();
    }

    /**
     * @return bool
     */
    public function hasBaseComponent(): bool
    {
        return $this->baseComponent instanceof ComponentValue;
    }

    /**
     * @return array
     * @throws InvalidSchemaAppClassException
     * @throws InvalidSchemaClassNameForGeneratedClassException
     * @throws InvalidSchemaNamespaceForGeneratedClassException
     */
    public function toArray(): array
    {
        return [
            'class' => $this->getAppClass(),
            'namespace' => $this->getNamespaceForGeneratedClass(),
            'classname' => $this->getClassNameForGeneratedClass(),
            'storePath' => $this->getWhereStoreGeneratedClass(),
            'extends' => $this->getClassToBeExtended(),
            'implements' => $this->implementsInterfaces,
            'traits' => $this->traits,
        ];
    }
}