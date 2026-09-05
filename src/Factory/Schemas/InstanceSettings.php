<?php

namespace Lkt\Factory\Schemas;

use Lkt\Factory\Schemas\Exceptions\InvalidComponentException;
use Lkt\Factory\Schemas\Exceptions\InvalidSchemaAppClassException;
use Lkt\Factory\Schemas\Exceptions\InvalidSchemaClassNameForGeneratedClassException;
use Lkt\Factory\Schemas\Exceptions\InvalidSchemaNamespaceForGeneratedClassException;

final class InstanceSettings
{
    private string|null $appClass = null;
    private string|null $namespaceForGeneratedClass = null;
    private string|null $classForGeneratedClass = null;
    private string|null $whereStoreGeneratedClass = null;
    private string|null $classToBeExtended = null;
    private string|null $baseComponent = null;
    private string|null $queryCallerClassName = null;
    private string|null $whereClassName = null;
    protected array $implementsInterfaces = [];
    protected array $traits = [];

    protected bool $abstractInstanceExtends = false;

    public function setAbstractInstanceExtends(bool $status = true): static
    {
        $this->abstractInstanceExtends = $status;
        return $this;
    }

    public function hasAbstractInstanceExtends(): bool
    {
        return $this->abstractInstanceExtends;
    }


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
        if (!$appClass) throw new InvalidSchemaAppClassException();
        $this->appClass = $appClass;
    }

    /**
     * @throws InvalidSchemaAppClassException
     */
    public function getAppClass(): string
    {
        if ($this->appClass) {
            return $this->appClass;
        }
        throw new InvalidSchemaAppClassException();
    }

    /**
     * @param string $appClass
     * @return InstanceSettings
     * @throws InvalidSchemaAppClassException
     */
    public static function define(string $appClass): static
    {
        return new InstanceSettings($appClass);
    }

    /**
     * @param string $appClass
     * @param string $generatedNamespace
     * @param string $generatedStoreDir
     * @return InstanceSettings
     * @throws InvalidSchemaAppClassException
     */
    public static function simple(string $appClass, string $generatedNamespace, string $generatedStoreDir): InstanceSettings
    {
        return (new InstanceSettings($appClass))
            ->setNamespaceForGeneratedClass($generatedNamespace)
            ->setWhereStoreGeneratedClass($generatedStoreDir);
    }

    public function setNamespaceForGeneratedClass(string $namespace): static
    {
        $this->namespaceForGeneratedClass = $namespace;
        return $this;
    }

    /**
     * @return string
     * @throws InvalidSchemaNamespaceForGeneratedClassException
     */
    public function getNamespaceForGeneratedClass(): string
    {
        if ($this->namespaceForGeneratedClass) {
            return $this->namespaceForGeneratedClass;
        }
        throw new InvalidSchemaNamespaceForGeneratedClassException();
    }

    public function setClassNameForGeneratedClass(string $name): static
    {
        $this->classForGeneratedClass = $name;
        return $this;
    }

    public function getClassNameForGeneratedClass(): string
    {
        if ($this->classForGeneratedClass) {
            return $this->classForGeneratedClass;
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
    public function setWhereStoreGeneratedClass(string $name): static
    {
        $this->whereStoreGeneratedClass = $name;
        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setQueryCallerClassName(string $name): static
    {
        $this->queryCallerClassName = $name;
        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setWhereClassName(string $name): static
    {
        $this->whereClassName = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getWhereStoreGeneratedClass(): string
    {
        if ($this->whereStoreGeneratedClass) {
            return $this->whereStoreGeneratedClass;
        }
        return '';
    }

    /**
     * @throws InvalidSchemaAppClassException
     * @throws InvalidSchemaClassNameForGeneratedClassException
     */
    public function getQueryCallerClassName(): string
    {
        if ($this->queryCallerClassName) {
            return $this->queryCallerClassName;
        }

        $generatedClassName = $this->getAppClass();
        if ($generatedClassName === '') {
            throw new InvalidSchemaClassNameForGeneratedClassException();
        }
        $generatedClassName = explode('\\', $generatedClassName);
        $generatedClassName = $generatedClassName[count($generatedClassName) - 1];
        return "{$generatedClassName}QueryBuilder";
    }

    public function getWhereClassName(): string
    {
        if ($this->whereClassName) {
            return $this->whereClassName;
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
    public function setClassToBeExtended(string $name): static
    {
        $this->classToBeExtended = $name;
        return $this;
    }

    public function getClassToBeExtended(): string
    {
        if ($this->classToBeExtended) {
            return $this->classToBeExtended;
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

    public function setBaseComponent(string $name): static
    {
        $this->baseComponent = $name;
        return $this;
    }

    /**
     * @return string
     * @throws InvalidComponentException
     */
    public function getBaseComponent(): string
    {
        if ($this->baseComponent) {
            return $this->baseComponent;
        }
        throw new InvalidComponentException();
    }

    /**
     * @return bool
     */
    public function hasBaseComponent(): bool
    {
        return trim($this->baseComponent) !== '';
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