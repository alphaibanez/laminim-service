<?php

namespace Lkt\Factory\Schemas;

use Lkt\Factory\Instantiator\Instantiator;
use Lkt\Factory\Schemas\ComputedFields\AbstractComputedField;
use Lkt\Factory\Schemas\Exceptions\DuplicatedAccessPolicyDefinitionException;
use Lkt\Factory\Schemas\Exceptions\InvalidComponentException;
use Lkt\Factory\Schemas\Exceptions\InvalidTableException;
use Lkt\Factory\Schemas\Exceptions\SchemaNotDefinedException;
use Lkt\Factory\Schemas\Exceptions\UndefinedAccessPolicyException;
use Lkt\Factory\Schemas\Fields\AbstractField;
use Lkt\Factory\Schemas\Fields\BooleanField;
use Lkt\Factory\Schemas\Fields\ColorField;
use Lkt\Factory\Schemas\Fields\ConcatField;
use Lkt\Factory\Schemas\Fields\ConstantValueField;
use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\EmailField;
use Lkt\Factory\Schemas\Fields\EncryptField;
use Lkt\Factory\Schemas\Fields\FileField;
use Lkt\Factory\Schemas\Fields\FloatField;
use Lkt\Factory\Schemas\Fields\ForeignKeyField;
use Lkt\Factory\Schemas\Fields\ForeignKeysField;
use Lkt\Factory\Schemas\Fields\HTMLField;
use Lkt\Factory\Schemas\Fields\IdField;
use Lkt\Factory\Schemas\Fields\ImageField;
use Lkt\Factory\Schemas\Fields\IntegerChoiceField;
use Lkt\Factory\Schemas\Fields\IntegerField;
use Lkt\Factory\Schemas\Fields\JSONField;
use Lkt\Factory\Schemas\Fields\MethodGetterField;
use Lkt\Factory\Schemas\Fields\PivotField;
use Lkt\Factory\Schemas\Fields\PivotLeftIdField;
use Lkt\Factory\Schemas\Fields\PivotPositionField;
use Lkt\Factory\Schemas\Fields\PivotRightIdField;
use Lkt\Factory\Schemas\Fields\RelatedField;
use Lkt\Factory\Schemas\Fields\RelatedKeysField;
use Lkt\Factory\Schemas\Fields\RelatedKeysMergeField;
use Lkt\Factory\Schemas\Fields\StringChoiceField;
use Lkt\Factory\Schemas\Fields\StringField;
use Lkt\Factory\Schemas\Fields\UnixTimeStampField;
use Lkt\Factory\Schemas\Fields\UrlField;
use Lkt\Factory\Schemas\Fields\ValueListField;
use Lkt\Factory\Schemas\ValueObjects\AccessPolicy;
use Lkt\Factory\Schemas\ValueObjects\AccessPolicyUsage;
use Lkt\Factory\Schemas\Values\ComponentValue;
use Lkt\Factory\Schemas\Values\TableValue;
use Lkt\QueryBuilding\Query;
use function Lkt\Tools\Arrays\getArrayFirstPosition;

final class Schema
{
    /** @var Schema[] */
    private static array $stack = [];
    protected array $excludeFieldFromViewFeed = [];

    protected string $slugPattern = '';

    protected array $complexPrimaryKey = [];

    /** @var AccessPolicy[] */
    protected array $accessPolicies = [];

    protected ?TableValue $table = null;

    protected ?ComponentValue $component = null;

    protected $databaseConnector = '';

    /** @var AbstractField[] */
    protected $idFields = [];
    protected $idColumns = [];
    protected $idColumnsInTable = [];

    /** @var AbstractField[] */
    protected $fields = [];

    // Pivot exclusive data
    protected bool $pivot = false;

    /** @var InstanceSettings */
    protected $instanceSettings;

    protected $countableField = '';
    protected int $itemsPerPage = 0;

    protected bool $registeredAsLib = false;

    protected string $ownershipField = '';

    public function setOwnershipField(string $fieldName): static
    {
        $this->ownershipField = $fieldName;
        return $this;
    }

    public function getOwnershipField(): AbstractField|null
    {
        if (!$this->ownershipField) return null;
        return $this->getField($this->ownershipField);
    }

    /**
     * @return Schema[]
     */
    public static function getStack(): array
    {
        return self::$stack;
    }

    public static function getCount(): int
    {
        return count(self::$stack);
    }

    /**
     * @param Schema $schema
     * @return void
     */
    public static function add(Schema $schema): void
    {
        $code = $schema->getComponent();
        self::$stack[$code] = $schema;
    }

    /**
     * @throws SchemaNotDefinedException
     */
    public static function get(string $code): self
    {
        if (!self::$stack[$code] instanceof Schema) {
            throw new SchemaNotDefinedException($code);
        }
        return self::$stack[$code];
    }

    /**
     * @throws SchemaNotDefinedException
     */
    public static function getFromTable(string $table): self
    {
        $result = array_filter(self::$stack, function (Schema $schema) use ($table) {
            return $schema->getTable() === $table;
        });

        if (count($result) > 0) {
            $result = reset($result);

            if (!$result instanceof Schema) {
                throw new SchemaNotDefinedException($table);
            }
            return $result;
        }
        throw new SchemaNotDefinedException($table);
    }

    public static function exists(string $code): bool
    {
        return self::$stack[$code] instanceof Schema;
    }

    /**
     * @param string $table
     * @param string $component
     * @return static
     * @throws InvalidComponentException
     * @throws InvalidTableException
     */
    public static function table(string $table, string $component): self
    {
        return new static($table, $component);
    }

    /**
     * @param string $component
     * @return self
     * @throws InvalidComponentException
     * @throws InvalidTableException
     * @deprecated
     */
    public static function local(string $component): self
    {
        return new static('_', $component);
    }

    /**
     * @param string $component
     * @return self
     * @throws InvalidComponentException
     * @throws InvalidTableException
     * @deprecated
     */
    public static function module(string $component): self
    {
        return new static('_', $component);
    }

    /**
     * @param string $table
     * @param string $component
     * @return static
     * @throws InvalidComponentException
     * @throws InvalidTableException
     */
    public static function pivotTable(string $table, string $component): self
    {
        return new static($table, $component, true);
    }

    /**
     * @param string $table
     * @param string $component
     * @param bool $isPivot
     * @throws InvalidComponentException
     * @throws InvalidTableException
     */
    public function __construct(string $table, string $component, bool $isPivot = false)
    {
        $this->table = new TableValue($table);
        $this->component = new ComponentValue($component);
        $this->pivot = $isPivot;
        $debug = debug_backtrace()[1]['file'];
        $path = realpath($debug);
        $this->registeredAsLib = str_contains($path, '/vendor');
    }

    public function addAccessPolicy(string|AccessPolicy $policy, array $availableFields = [], array $availableCompositionFields = []): static
    {
        if (isset($this->accessPolicies[$policy])) {
            throw DuplicatedAccessPolicyDefinitionException::getInstance($this->getComponent(), $policy);
        }

        if (is_string($policy)) {
            $this->accessPolicies[$policy] = new AccessPolicy($policy, $availableFields, $availableCompositionFields);
        } else {
            $this->accessPolicies[$policy->name] = $policy;
        }

        return $this;
    }

    public function getAccessPolicy(string $name): AccessPolicy
    {
        if (!isset($this->accessPolicies[$name])) {
            throw UndefinedAccessPolicyException::getInstance($this->getComponent(), $name);
        }

        return $this->accessPolicies[$name];
    }

    public function hasAccessPolicy(string $name): bool
    {
        return $this->accessPolicies[$name] instanceof AccessPolicy;
    }

    public function hasRelatedAccessPolicy(): bool
    {
        return $this->hasAccessPolicy('lkt-related');
    }

    public function isLib(): bool
    {
        return $this->registeredAsLib;
    }

    public function setRelatedAccessPolicy(array $availableFields = [], array $availableCompositionFields = []): static
    {
        $policy = 'lkt-related';
        $this->accessPolicies[$policy] = new AccessPolicy($policy, $availableFields, $availableCompositionFields);
        return $this;
    }

    public function getAccessPolicyForRelationalField(string|AccessPolicyUsage|AccessPolicy $accessPolicy, RelatedField|ForeignKeyField|ForeignKeysField|PivotField $field): ?AccessPolicy
    {
        if (is_string($accessPolicy)) $accessPolicy = $this->getAccessPolicy($accessPolicy);
        elseif ($accessPolicy instanceof AccessPolicyUsage) $accessPolicy = $this->getAccessPolicy($accessPolicy->name);

        $fieldAccessPolicy = $field->getAssociatedAccessPolicy($accessPolicy->name);
        if ($fieldAccessPolicy) {
            $associatedSchema = static::get($field->getComponent());
            return $associatedSchema->getAccessPolicy($fieldAccessPolicy);
        }
        return null;
    }

    public function setCountableField(string $fieldName): self
    {
        $this->countableField = $fieldName;
        return $this;
    }

    public function getCountableField(): string
    {
        return $this->countableField;
    }

    public function hasCountableField(): bool
    {
        return $this->countableField !== '';
    }

    public function setItemsPerPage(int $itemsPerPage): self
    {
        $this->itemsPerPage = $itemsPerPage;
        return $this;
    }

    public function getItemsPerPage(): int
    {
        return $this->itemsPerPage;
    }

    public function hasItemsPerPage(): bool
    {
        return $this->itemsPerPage > 0;
    }

    /**
     * @param InstanceSettings $config
     * @return $this
     */
    public function setInstanceSettings(InstanceSettings $config): self
    {
        $this->instanceSettings = $config;
        return $this;
    }

    /**
     * @return InstanceSettings|null
     */
    public function getInstanceSettings(): ?InstanceSettings
    {
        return $this->instanceSettings;
    }


    /**
     * @param AbstractField $field
     * @return $this
     * @throws \Exception
     */
    public function addField(AbstractField $field): self
    {
        $name = $field->getName();
        if (isset($this->fields[$name]) && $this->fields[$name] instanceof AbstractField) {
            throw new \Exception("Field '{$name}' already registered in schema '{$this->getComponent()}'");
        }
        $this->fields[$name] = $field;
        return $this;
    }

    /**
     * @return array
     * @throws Exceptions\InvalidSchemaAppClassException
     * @throws Exceptions\InvalidSchemaClassNameForGeneratedClassException
     * @throws Exceptions\InvalidSchemaNamespaceForGeneratedClassException
     * @throws InvalidComponentException
     */
    public function toArray(): array
    {
        return [
            'table' => $this->table->getValue(),
            'idColumn' => $this->pivot ? $this->idFields : $this->idFields[0],
            'pivot' => $this->pivot,
            'instance' => $this->instanceSettings->toArray(),
            'base' => $this->instanceSettings->hasBaseComponent() ? $this->instanceSettings->getBaseComponent() : '',
            'fields' => $this->fields,
        ];
    }

    /**
     * @return bool
     */
    public function isPivot(): bool
    {
        return $this->pivot === true;
    }

    /**
     * @return array<ForeignKeyField|ForeignKeysField|PivotField|RelatedField|RelatedKeysField|RelatedKeysMergeField|StringField|BooleanField|ColorField|JSONField|ConcatField|DateTimeField|EmailField|EncryptField|FileField|FloatField|IntegerField|HTMLField|IdField|ImageField|IntegerChoiceField|MethodGetterField|UnixTimeStampField|UrlField|ValueListField|ConstantValueField>
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @return array<ForeignKeyField|ForeignKeysField|PivotField|RelatedField|RelatedKeysField|RelatedKeysMergeField|StringField|BooleanField|ColorField|JSONField|ConcatField|DateTimeField|EmailField|EncryptField|FileField|FloatField|IntegerField|HTMLField|IdField|ImageField|IntegerChoiceField|MethodGetterField|UnixTimeStampField|UrlField|ValueListField|ConstantValueField>
     * @throws InvalidComponentException
     * @throws SchemaNotDefinedException
     */
    public function getAllFields(): array
    {
        $r = $this->fields;

        if ($this->instanceSettings instanceof InstanceSettings) {
            if ($this->instanceSettings->hasLegalExtendClass()) {

                $code = $this->instanceSettings->getClassToBeExtended()::COMPONENT;
                if ($code) {
                    $schema = Schema::get($code);
                    $fields = $schema->getAllFields();
                    foreach ($fields as $column => $field) {
                        if (!array_key_exists($column, $r)) {
                            $r[$column] = $field;
                        }
                    }
                }
            }

            if ($this->instanceSettings->hasBaseComponent()) {
                $baseSchema = Schema::get($this->instanceSettings->getBaseComponent());
                $fields = $baseSchema->getAllFields();
                foreach ($fields as $column => $field) {
                    if (!array_key_exists($column, $r)) {
                        $r[$column] = $field;
                    }
                }
            }
        }
        return $r;
    }

    /**
     * @return array<StringField|BooleanField|ColorField|JSONField|ConcatField|DateTimeField|EmailField|EncryptField|FileField|FloatField|IntegerField|HTMLField|IdField|ImageField|IntegerChoiceField|MethodGetterField|UnixTimeStampField|UrlField|ValueListField|ConstantValueField>
     * @throws InvalidComponentException
     * @throws SchemaNotDefinedException
     */
    public function getNonRelationalFields(): array
    {
        return array_filter($this->getAllFields(), function (AbstractField $field) {
            if ($field instanceof ForeignKeyField
                || $field instanceof ForeignKeysField
                || $field instanceof PivotField
                || $field instanceof RelatedField
                || $field instanceof RelatedKeysField
                || $field instanceof RelatedKeysMergeField) {
                return false;
            }
            return true;
        });
    }

    /**
     * @return array<ForeignKeyField|ForeignKeysField|PivotField|RelatedField|RelatedKeysField|RelatedKeysMergeField>
     * @throws InvalidComponentException
     * @throws SchemaNotDefinedException
     */
    public function getRelationalFields(): array
    {
        return array_filter($this->getAllFields(), function (AbstractField $field) {
            if ($field instanceof ForeignKeyField
                || $field instanceof ForeignKeysField
                || $field instanceof PivotField
                || $field instanceof RelatedField
                || $field instanceof RelatedKeysField
                || $field instanceof RelatedKeysMergeField) {
                return true;
            }
            return false;
        });
    }

    /**
     * @return array<ForeignKeyField|ForeignKeysField|PivotField|RelatedField|RelatedKeysField|RelatedKeysMergeField|StringField|BooleanField|ColorField|JSONField|ConcatField|DateTimeField|EmailField|EncryptField|FileField|FloatField|IntegerField|HTMLField|IdField|ImageField|IntegerChoiceField|MethodGetterField|UnixTimeStampField|UrlField|ValueListField>
     * @throws InvalidComponentException
     * @throws SchemaNotDefinedException
     */
    public function getMandatoryFields(): array
    {
        return array_filter($this->getAllFields(), function (AbstractField $field) {
            return method_exists($field, 'isMandatory') ? $field->isMandatory() : false;
        });
    }

    public function getPivotLeftIdField(): PivotLeftIdField
    {
        $r = array_values(array_filter($this->getFields(), function (AbstractField $field) {
            return $field instanceof PivotLeftIdField;
        }));

        return reset($r);
    }

    public function getPivotRightIdField(): PivotRightIdField
    {
        $r = array_values(array_filter($this->getFields(), function (AbstractField $field) {
            return $field instanceof PivotRightIdField;
        }));

        return reset($r);
    }

    /**
     * @return array<StringChoiceField|IntegerChoiceField>
     * @throws InvalidComponentException
     * @throws SchemaNotDefinedException
     */
    public function getChoiceFields(): array
    {
        return array_filter($this->getAllFields(), function (AbstractField $field) {
            if ($field instanceof StringChoiceField
                || $field instanceof IntegerChoiceField) {
                return true;
            }
            return false;
        });
    }

    /**
     * @return array<StringChoiceField|IntegerChoiceField>
     * @throws InvalidComponentException
     * @throws SchemaNotDefinedException
     */
    public function getChoiceFieldsWithDefaultValue(): array
    {
        return array_filter($this->getAllFields(), function (AbstractField $field) {
            if ($field instanceof StringChoiceField
                || $field instanceof IntegerChoiceField) {
                return $field->hasEmptyDefault();
            }
            return false;
        });
    }

    /**
     * @return AbstractField[]
     */
    public function getSameTableFields(): array
    {
        return array_filter($this->getAllFields(), function (AbstractField $field) {
            if ($field instanceof PivotField
                || $field instanceof RelatedField
                || $field instanceof RelatedKeysMergeField
                || $field instanceof AbstractComputedField
                || $field instanceof ConstantValueField) {
                return false;
            }
            return true;
        });
    }

    /**
     * @return array<ForeignKeyField|PivotField|RelatedField>
     * @throws InvalidComponentException
     * @throws SchemaNotDefinedException
     */
    public function getFilterableFields(): array
    {
        return array_filter($this->getAllFields(), function (AbstractField $field) {
            if ($field instanceof ForeignKeyField
                || $field instanceof PivotField
                || $field instanceof RelatedField) {
                return false;
            }
            return true;
        });
    }

    public function getFieldsWithDefaultValue(): array
    {
        return array_filter($this->getAllFields(), function (AbstractField $field) {
            return $field->hasDefaultValue();
        });
    }

    /**
     * @param string $field
     * @param bool $searchComposed
     * @return null|AbstractField|ForeignKeyField|ForeignKeysField|PivotField|RelatedField|RelatedKeysField|RelatedKeysMergeField|StringField|BooleanField|ColorField|JSONField|ConcatField|DateTimeField|EmailField|EncryptField|FileField|FloatField|IntegerField|HTMLField|IdField|ImageField|IntegerChoiceField|MethodGetterField|UnixTimeStampField|UrlField|ValueListField|ConstantValueField
     * @throws InvalidComponentException
     * @throws SchemaNotDefinedException
     */
    public function getField(string $field, bool $searchComposed = true): ?AbstractField
    {
        $haystack = $this->getAllFields();
        if (isset($haystack[$field])) return $haystack[$field];

        // Check if column is configured
        $found = array_filter($this->getFields(), function (AbstractField $f) use ($field) {
            return $f->getColumn() === $field;
        });
        if (count($found) > 0) return reset($found);

        // Catch foreign key cast to integer keys
        $l = strlen($field);
        $endsWithId = substr($field, $l - 2, 2) === 'Id';
        if ($endsWithId) {
            $keyWithoutId = substr($field, 0, $l - 2);
            if (isset($haystack[$keyWithoutId]) && $haystack[$keyWithoutId] instanceof ForeignKeyField) {
                return $haystack[$keyWithoutId];
            }
        }

        $l = strlen($field);
        $endsWithIds = substr($field, $l - 3, 3) === 'Ids';
        if ($endsWithIds) {
            $keyWithoutIds = substr($field, 0, $l - 3);
            if (isset($haystack[$keyWithoutIds]) && $haystack[$keyWithoutIds] instanceof ForeignKeysField) {
                return $haystack[$keyWithoutIds];
            }
        }

        if ($searchComposed) {
            return $this->getComposedField($field);
        }
        return null;
    }

    public function hasField(string $fieldName): bool
    {
        return $this->getField($fieldName) !== null;
    }

    public function hasFieldDefined(string $fieldName): bool
    {
        if ($this->getInstanceSettings()->hasBaseComponent()) {
            $baseDefined = (static::get($this->getInstanceSettings()->getBaseComponent()))->hasFieldDefined($fieldName);
            if ($baseDefined) return true;
        }
        $l = strlen($fieldName);
        $endsWithId = substr($fieldName, $l - 2, 2) === 'Id';
        if ($endsWithId) {
            $keyWithoutId = substr($fieldName, 0, $l - 2);
            if (isset($this->fields[$keyWithoutId]) && $this->fields[$keyWithoutId] instanceof ForeignKeyField) {
                return $this->fields[$keyWithoutId] !== null;
            }
        }

        $l = strlen($fieldName);
        $endsWithIds = substr($fieldName, $l - 3, 3) === 'Ids';
        if ($endsWithIds) {
            $keyWithoutIds = substr($fieldName, 0, $l - 3);
            if (isset($this->fields[$keyWithoutIds]) && $this->fields[$keyWithoutIds] instanceof ForeignKeysField) {
                return $this->fields[$keyWithoutIds] !== null;
            }
        }

        return $this->fields[$fieldName] !== null;
    }

    public function getFeedField(string $field): ?AbstractField
    {
        $haystack = $this->getAllFields();

        if (isset($haystack[$field])) {
            if ($haystack[$field] instanceof RelatedKeysField) {
                return null;
            }
            return $haystack[$field];
        }

        // Catch foreign keys cast to integer keys
        $l = strlen($field);
        $endsWithId = substr($field, $l - 2, 2) === 'Id';

        if (!$endsWithId) return null;

        $keyWithoutId = substr($field, 0, $l - 2);
        if (isset($haystack[$keyWithoutId]) && $haystack[$keyWithoutId] instanceof ForeignKeyField) {
            return $haystack[$keyWithoutId];
        }
        return null;
    }

    public function getFileField(string $field): ?FileField
    {
        $r = $this->getField($field);
        if ($r instanceof FileField) return $r;
        return null;
    }

    /**
     * @return FileField[]
     */
    public function getFileFields(): array
    {
        return array_filter($this->getFields(), function ($field) {
            return $field instanceof FileField;
        });
    }

    public function getRelatedField(string $field): ?RelatedField
    {
        $r = $this->getField($field);
        if ($r instanceof RelatedField) return $r;
        return null;
    }

    public function getForeignKeyField(string $field): ?ForeignKeyField
    {
        $r = $this->getField($field);
        if ($r instanceof ForeignKeyField) return $r;
        return null;
    }

    public function getForeignKeysField(string $field): ?ForeignKeysField
    {
        $r = $this->getField($field);
        if ($r instanceof ForeignKeysField) return $r;
        return null;
    }

    public function getForeignKeysFieldPointingToComponent(string $component): ?ForeignKeysField
    {
        $results = $this->getFieldsPointingToComponent($component);
        foreach ($results as $result) {
            if ($result instanceof ForeignKeysField) return $result;
        }
        return null;
    }

    public function getPivotField(string $field): ?PivotField
    {
        $r = $this->getField($field);
        if ($r instanceof PivotField) return $r;
        return null;
    }

    /**
     * @return array<PivotField>
     * @throws InvalidComponentException
     * @throws SchemaNotDefinedException
     */
    public function getPivotFields(): array
    {
        return array_filter($this->getAllFields(), function (AbstractField $field) {
            if ($field instanceof PivotField) {
                return true;
            }
            return false;
        });
    }

    /**
     * @return array<RelatedField|ForeignKeyField>
     */
    public function getCompositionFields(): array
    {
        return array_filter($this->getFields(), function ($field) {
            if ($field instanceof RelatedField || $field instanceof ForeignKeyField) {
                return $field->hasCompositionContent();
            }
            return false;
        });
    }

    public function getCompositionField(string $fieldName): null|ForeignKeyField|RelatedField
    {
        $field = $this->getField($fieldName);
        if (!$field instanceof RelatedField && !$field instanceof ForeignKeyField) return null;
        if (!$field->hasCompositionContent()) return null;
        return $field;
    }

    public function getCompositionValueFields(string $fieldName): array
    {
        $field = $this->getField($fieldName);

        if (!$field instanceof RelatedField && !$field instanceof ForeignKeyField) return [];
        if (!$field->hasCompositionContent()) return [];

        $r = [];
        foreach ($field->getCompositionValues() as $paramName => $compositionValue) {
            $r[$paramName] = $this->getField($compositionValue);
        }

        return $r;
    }

    public function getAllCompositionValueFields(): array
    {
        $r = [];
        foreach ($this->getCompositionFields() as $field) {
            if (!$field instanceof RelatedField && !$field instanceof ForeignKeyField) continue;
            if (!$field->hasCompositionContent()) continue;

            foreach ($field->getCompositionValues() as $paramName => $compositionValue) {
                $r[$paramName] = $this->getField($compositionValue);
            }
        }

        return $r;
    }

    public function getFieldComposedFields(AbstractField $field): array
    {
        if (!$field instanceof RelatedField && !$field instanceof ForeignKeyField) {
            return [];
        }

        $r = [];

        $schema = Schema::get($field->getComponent());

        foreach ($field->getCompositionContent() as $fieldName => $composedFieldName) {
            $composedField = $schema?->getField($composedFieldName);
            if (!$composedField) {

                $nestedCompositionField = $schema->getCompositionFieldComposingThisField($composedFieldName);
                $nestedComposedSchema = Schema::get($nestedCompositionField->getComponent());
                $composedField = $nestedComposedSchema->getField($composedFieldName);
            }

            if ($composedField) {
                $r[$fieldName] = $composedField;
            }
        }
        return $r;
    }

    public function getComposedFields(): array
    {
        $r = [];
        foreach ($this->getCompositionFields() as $compositionField) {
            $r = array_merge($r, $this->getFieldComposedFields($compositionField));
        }

        return $r;
    }

    public function getComposedField(string $fieldName): ?AbstractField
    {
        $r = array_filter($this->getComposedFields(), function ($field) use ($fieldName) {
            return $field?->getName() === $fieldName;
        });
        $r = reset($r);
        return $r === false ? null : $r;
    }

    public function isComposedField(string $fieldName): bool
    {
        return is_object($this->getComposedField($fieldName));
    }

    public function getCompositionFieldComposingThisField(string $fieldName): null|RelatedField|ForeignKeyField
    {
        foreach ($this->getCompositionFields() as $compositionField) {
            $compositionContent = $compositionField->getCompositionContent();
            if (is_array($compositionContent) && in_array($fieldName, $compositionContent)) {
                return $compositionField;
            }
        }

        return null;
    }

    /**
     * @return AbstractField[]
     * @throws InvalidComponentException
     * @throws SchemaNotDefinedException
     */
    public function getIdentifiers(): array
    {
        if (count($this->idFields) > 0) return $this->idFields;

        /** @var AbstractField[] $stack */
        $stack = $this->getAllFields();

        if ($this->isPivot()) {
            $fields = array_filter($stack, function (AbstractField $field) {
                return $field instanceof PivotLeftIdField || $field instanceof PivotRightIdField;
            });

            $this->idColumns = array_keys($fields);
            $this->idFields = array_values($fields);
            $this->idColumnsInTable = array_map(function ($field) {
                return $field->getColumn();
            }, $fields);
            return $this->idFields;
        }

        if ($this->hasComplexPrimaryKey()) {
            $fields = array_filter($stack, function (AbstractField $field) {
                return in_array($field->getName(), $this->complexPrimaryKey);
            });

            $this->idColumns = array_keys($fields);
            $this->idFields = array_values($fields);
            $this->idColumnsInTable = array_map(function ($field) {
                return $field->getColumn();
            }, $fields);
            return $this->idFields;
        }

        $fields = array_filter($stack, function (AbstractField $field) {
            return $field instanceof IdField || $field->isIdentifier();
        });

        $this->idColumns = array_values(array_map(function (AbstractField $field) {
            $r = $field->getName();
            if ($field instanceof ForeignKeyField) $r .= 'Id';
            return $r;
        }, $fields));
        $this->idFields = array_values($fields);
        $this->idColumnsInTable = array_map(function ($field) {
            return $field->getColumn();
        }, $fields);
        return $this->idFields;
    }

    public function hasComplexPrimaryKey(): bool
    {
        return count($this->complexPrimaryKey) > 1;
    }

    public function setComplexPrimaryKey(array $fieldNames): static
    {
        $this->complexPrimaryKey = $fieldNames;
        return $this;
    }

    /**
     * @return AbstractField[]
     * @throws InvalidComponentException
     * @throws SchemaNotDefinedException
     */
    public function getComplexPrimaryKeyFields(): array
    {
        if ($this->hasComplexPrimaryKey()) {
            $this->getIdentifiers();
            return $this->idFields;
        }
        return [];
    }

    /**
     * @return string
     * @throws InvalidComponentException
     */
    public function getIdString(): string
    {
        $this->getIdentifiers();
        return trim(implode('-', $this->idColumns));
    }

    /**
     * @return string
     * @throws InvalidComponentException
     */
    public function getIdInTableString(): string
    {
        $this->getIdentifiers();
        return trim(implode('-', $this->idColumnsInTable));
    }

    /**
     * @return array|mixed
     * @throws InvalidComponentException
     */
    public function getIdColumn()
    {
        $this->getIdentifiers();
        return $this->idColumns;
    }

    /**
     * @return string
     */
    public function getComponent(): string
    {
        return $this->component->getValue();
    }

    /**
     * @param string $component
     * @return array
     * @throws InvalidComponentException|SchemaNotDefinedException
     */
    public function getFieldsPointingToComponent(string $component): array
    {
        /** @var AbstractField[] $fields */
        $fields = $this->getRelationalFields();
        return array_filter($fields, function ($field) use ($component) {
            if ($field instanceof ForeignKeyField
                || $field instanceof ForeignKeysField
                || $field instanceof RelatedKeysField
                || $field instanceof PivotField
                || $field instanceof RelatedField) {
                return $field->getComponent() === $component;
            }
            return false;
        });
    }

    /**
     * @param string $component
     * @return AbstractField|null
     * @throws InvalidComponentException|SchemaNotDefinedException
     */
    public function getOneFieldPointingToComponent(string $component): ?AbstractField
    {
        $r = $this->getFieldsPointingToComponent($component);
        if (count($r) > 0) {
            return getArrayFirstPosition($r);
        }
        return null;
    }

    public function getOnePositionField(): ?AbstractField
    {
        /** @var AbstractField[] $fields */
        $fields = $this->getFields();
        $r = array_values(array_filter($fields, function ($field) {
            return $field instanceof PivotPositionField;
        }));

        return reset($r);
    }

    /**
     * @param string $component
     * @param bool $matchOne
     * @return array|mixed
     * @throws InvalidComponentException
     * @throws SchemaNotDefinedException
     */
    public function getColumnsPointingToComponent(string $component, bool $matchOne = false)
    {
        /** @var AbstractField[] $fields */
        $fields = $this->getAllFields();
        $results = array_map(function ($item) {
            return $item->getColumn();
        }, array_filter($fields, function ($field) use ($component) {
            if ($field instanceof ForeignKeyField
                || $field instanceof ForeignKeysField
                || $field instanceof RelatedKeysField
                || $field instanceof PivotField
                || $field instanceof RelatedField) {
                return $field->getComponent() === $component;
            }
            return false;
        }));

        if ($matchOne) {
            $results = array_values($results);
            if (count($results) > 0) {
                return $results[0];
            }
        }
        return array_values($results);
    }

    /**
     * @return string
     */
    public function getTable(): string
    {
        return $this->table->getValue();
    }

    /**
     * @param string $connectorName
     * @return $this
     */
    public function setDatabaseConnector(string $connectorName): self
    {
        $this->databaseConnector = $connectorName;
        return $this;
    }

    /**
     * @return string
     */
    public function getDatabaseConnector(): string
    {
        return $this->databaseConnector;
    }

    public function getAccessPolicyFields(string|AccessPolicyUsage $accessPolicy): array
    {
        $accessPolicy = $accessPolicy instanceof AccessPolicyUsage ? $this->getAccessPolicy($accessPolicy->name) : $this->getAccessPolicy($accessPolicy);

        $r = [];
        foreach ($accessPolicy->availableFields as $key => $val) {
            $searchKey = is_numeric($key) ? $val : $key;
            $f = $accessPolicy->getSchemaField($this, $searchKey);
            if (!$f) $accessPolicy->getSchemaCompositionField($this, $searchKey);

            if ($f) {
                $r[$val] = $f;
            }
        }

        return $r;
    }

    public function getAccessPolicyExcludedFields(string|AccessPolicyUsage $accessPolicy): array
    {
        $accessPolicy = $accessPolicy instanceof AccessPolicyUsage ? $this->getAccessPolicy($accessPolicy->name) : $this->getAccessPolicy($accessPolicy);

        return array_filter($this->getAllFields(), function (AbstractField $field) use ($accessPolicy) {
            return !$accessPolicy->includesFieldName($field->getName());
        });
    }

    public function getAccessPolicyComposedFields(string|AccessPolicyUsage $accessPolicy): array
    {
        $accessPolicy = $accessPolicy instanceof AccessPolicyUsage ? $this->getAccessPolicy($accessPolicy->name) : $this->getAccessPolicy($accessPolicy);

        return array_filter($this->getComposedFields(), function (AbstractField $field) use ($accessPolicy) {
            return $accessPolicy->includesCompositionField($field);
        });
    }

    /**
     * @param string $value
     * @param string $label
     * @param array $additionalFields
     * @return $this
     * @deprecated
     */
    public function setFieldsForRelatedMode(string $value, string $label, array $additionalFields = []): static
    {
        $this->setRelatedAccessPolicy([$value => 'value', $label => 'label', ...$additionalFields]);
        return $this;
    }

    /**
     * @param string $view
     * @param array $fields
     * @return $this
     * @deprecated
     */
    public function setExcludedFieldsForViewFeed(string $view, array $fields): static
    {
        $this->excludeFieldFromViewFeed[$view] = $fields;
        return $this;
    }

    public function register(): static
    {
        static::$stack[$this->getComponent()] = $this;
        return $this;
    }

    public function getItemInstance($id = null)
    {
        return Instantiator::make($this->getComponent(), $id);
    }

    public function getQueryBuilder(): Query
    {
        list($queryBuilder) = Instantiator::getQueryCaller($this->getComponent());
        return $queryBuilder;
    }

    public function setSlugPattern(string $pattern): static
    {
        $this->slugPattern = $pattern;
        return $this;
    }

    public function getSlugPattern(): string
    {
        return $this->slugPattern;
    }
}