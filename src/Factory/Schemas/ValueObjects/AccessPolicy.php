<?php

namespace Lkt\Factory\Schemas\ValueObjects;

use Lkt\Attributes\Experimental;
use Lkt\Attributes\Recommended;
use Lkt\Factory\Schemas\Fields\AbstractField;
use Lkt\Factory\Schemas\Schema;

class AccessPolicy
{
    public string $name;

    /**
     * Indicates which fields should be included while reading and writing data.
     *
     * @var string[]
     * Formatting:
     *   Case 1: Numeric index means "get the field included in the value"
     *   Case 2: String index means "get the field defined in the key and uses the value as the name
     *           This applies in both cases: reading and writing data
     */
    public array $availableFields;

    /**
     * Represents which fields holding a composition config should add their composed values while reading or
     * have in count while writing.
     * Keeps the same format as $availableFields var
     *
     * @var string[]
     */
    public array $availableCompositionFields;

    public array $additionalFieldsOnWrite = [];
    public array $additionalFieldsOnCreate = [];
    public array $additionalFieldsOnUpdate = [];
    public array $additionalFieldsOnRead = [];

    public array $excludedFieldsOnWrite = [];
    public array $excludedFieldsOnCreate = [];
    public array $excludedFieldsOnUpdate = [];
    public array $excludedFieldsOnRead = [];

    public array $aliases = [];
    public array $extends = [];

    protected function __construct(string $name, array $availableFields, array $availableCompositionFields)
    {
        $this->name = $name;
        $this->availableFields = $availableFields;
        $this->availableCompositionFields = $availableCompositionFields;
    }

    #[Recommended('0.1.21')]
    public static function define(string $name, array $availableFields, array $availableCompositionFields): static
    {
        return new static($name, $availableFields, $availableCompositionFields);
    }

    #[Experimental('0.1.21')]
    public function setAliases(array $aliases): static
    {
        $this->aliases = $aliases;
        return $this;
    }

    #[Experimental('0.1.21')]
    public function setExtendedAccessPolicies(array $policies): static
    {
        $this->extends = $policies;
        return $this;
    }

    #[Experimental('0.1.21')]
    public function setAdditionalFieldsOnWrite(array $fields): static
    {
        $this->additionalFieldsOnWrite = $fields;
        return $this;
    }

    #[Experimental('0.1.21')]
    public function setAdditionalFieldsOnCreate(array $fields): static
    {
        $this->additionalFieldsOnCreate = $fields;
        return $this;
    }

    #[Experimental('0.1.21')]
    public function setAdditionalFieldsOnUpdate(array $fields): static
    {
        $this->additionalFieldsOnUpdate = $fields;
        return $this;
    }

    #[Experimental('0.1.21')]
    public function setAdditionalFieldsOnRead(array $fields): static
    {
        $this->additionalFieldsOnRead = $fields;
        return $this;
    }

    #[Experimental('0.1.21')]
    public function setExcludedFieldsOnWrite(array $fields): static
    {
        $this->excludedFieldsOnWrite = $fields;
        return $this;
    }

    #[Experimental('0.1.21')]
    public function setExcludedFieldsOnCreate(array $fields): static
    {
        $this->excludedFieldsOnCreate = $fields;
        return $this;
    }

    #[Experimental('0.1.21')]
    public function setExcludedFieldsOnUpdate(array $fields): static
    {
        $this->excludedFieldsOnUpdate = $fields;
        return $this;
    }

    #[Experimental('0.1.21')]
    public function setExcludedFieldsOnRead(array $fields): static
    {
        $this->excludedFieldsOnRead = $fields;
        return $this;
    }

    /**
     * @laminim
     *
     * Modifier can be 'mk', 'up', 'w', 'r'
     * used on file 'ItemWithAccessPolicyTrait', method 'setAccessPolicy'
     * @param string $modifier
     * @return array
     */
    #[Experimental('0.1.21')]
    public function getAvailableFields(string $modifier): array
    {
        switch ($modifier) {
            case 'w':
                return $this->buildAvailableFields($this->availableFields, $this->additionalFieldsOnWrite, $this->excludedFieldsOnWrite);

            case 'mk':
                return $this->buildAvailableFields($this->availableFields, $this->additionalFieldsOnCreate, $this->excludedFieldsOnCreate);

            case 'up':
                return $this->buildAvailableFields($this->availableFields, $this->additionalFieldsOnUpdate, $this->excludedFieldsOnUpdate);

            case 'r':
                return $this->buildAvailableFields($this->availableFields, $this->additionalFieldsOnRead, $this->excludedFieldsOnRead);

            default:
                return $this->availableFields;
        }
    }

    private function buildAvailableFields(array $haystack, array $additional, array $excluded): array
    {
        $r = [
            ...$haystack,
            ...$additional,
        ];

        foreach ($excluded as $k => $f) {

            if (array_key_exists($k, $r)) {
                unset($haystack[$k]);
            }

            elseif (in_array($k, $r)) {
                $keys = array_keys($r, $k);
                $key = reset($keys);
                unset($haystack[$key]);
            }
        }

        return $r;
    }

    public function includesField(AbstractField $field): bool
    {
        return in_array($field->getName(), $this->availableFields);
    }

    public function includesCompositionField(AbstractField $field, string|null $aliasKey = null): bool
    {
        if ($aliasKey && in_array($aliasKey, $this->availableCompositionFields)) {
            return true;
        }
        return in_array($field->getName(), $this->availableCompositionFields);
    }

    public function includesFieldName(string $field): bool
    {
        $foreignIdMatcher = "{$field}Id";
        if (substr($field, -2) === 'Id') {
            $foreignIdMatcher = substr($field, 0, strlen($field) -2);
        }
        return in_array($field, array_values($this->availableFields))
            || array_key_exists($field, $this->availableFields)

            || in_array($foreignIdMatcher, array_values($this->availableFields))
            || array_key_exists($foreignIdMatcher, $this->availableFields);
    }

    public function includesCompositionFieldName(string $field): bool
    {
        $foreignIdMatcher = "{$field}Id";
        if (substr($field, -2) === 'Id') {
            $foreignIdMatcher = substr($field, 0, strlen($field) -2);
        }
        return in_array($field, $this->availableCompositionFields)
            || array_key_exists($field, $this->availableCompositionFields)
            || in_array($foreignIdMatcher, $this->availableCompositionFields)
            || array_key_exists($foreignIdMatcher, $this->availableCompositionFields);
    }

    public function getSchemaField(Schema $schema, string $fieldName): \Lkt\Factory\Schemas\Fields\IntegerField|\Lkt\Factory\Schemas\Fields\MethodGetterField|\Lkt\Factory\Schemas\Fields\RelatedKeysField|AbstractField|\Lkt\Factory\Schemas\Fields\StringField|\Lkt\Factory\Schemas\Fields\PivotField|\Lkt\Factory\Schemas\Fields\ForeignKeysField|\Lkt\Factory\Schemas\Fields\DateTimeField|\Lkt\Factory\Schemas\Fields\ColorField|\Lkt\Factory\Schemas\Fields\RelatedField|\Lkt\Factory\Schemas\Fields\FloatField|\Lkt\Factory\Schemas\Fields\RelatedKeysMergeField|\Lkt\Factory\Schemas\Fields\ConcatField|\Lkt\Factory\Schemas\Fields\JSONField|\Lkt\Factory\Schemas\Fields\BooleanField|\Lkt\Factory\Schemas\Fields\FileField|\Lkt\Factory\Schemas\Fields\ValueListField|null
    {
        if (array_key_exists($fieldName, $this->availableFields)) {
            $key = $fieldName;
        }

        if (in_array($fieldName, $this->availableFields)) {
            $keys = array_keys($this->availableFields, $fieldName);
            $key = reset($keys);

            if (is_numeric($key)) $key = $fieldName;
        }


        $foreignIdMatcher = "{$fieldName}Id";
        if (substr($fieldName, -2) === 'Id') {
            $foreignIdMatcher = substr($fieldName, 0, strlen($fieldName) -2);
        }
        if (array_key_exists($foreignIdMatcher, $this->availableFields)) {
            $key = $fieldName;
        }

        if (in_array($foreignIdMatcher, $this->availableFields)) {
            $keys = array_keys($this->availableFields, $foreignIdMatcher);
            $key = reset($keys);

            if (is_numeric($key)) $key = $fieldName;
        }

        if (!$key) return null;
        return $schema->getField($key);
    }

    public function getSchemaCompositionField(Schema $schema, string $fieldName): \Lkt\Factory\Schemas\Fields\IntegerField|\Lkt\Factory\Schemas\Fields\MethodGetterField|\Lkt\Factory\Schemas\Fields\RelatedKeysField|AbstractField|\Lkt\Factory\Schemas\Fields\StringField|\Lkt\Factory\Schemas\Fields\PivotField|\Lkt\Factory\Schemas\Fields\ForeignKeysField|\Lkt\Factory\Schemas\Fields\DateTimeField|\Lkt\Factory\Schemas\Fields\ColorField|\Lkt\Factory\Schemas\Fields\RelatedField|\Lkt\Factory\Schemas\Fields\FloatField|\Lkt\Factory\Schemas\Fields\RelatedKeysMergeField|\Lkt\Factory\Schemas\Fields\ConcatField|\Lkt\Factory\Schemas\Fields\JSONField|\Lkt\Factory\Schemas\Fields\BooleanField|\Lkt\Factory\Schemas\Fields\FileField|\Lkt\Factory\Schemas\Fields\ValueListField|null
    {
        $foreignIdMatcher = "{$fieldName}Id";
        if (substr($fieldName, -2) === 'Id') {
            $foreignIdMatcher = substr($fieldName, 0, strlen($fieldName) -2);
        }


        if (array_key_exists($fieldName, $this->availableCompositionFields)) {
            $key = $fieldName;
        }

        if (in_array($fieldName, $this->availableCompositionFields)) {
            $key = $fieldName;
        }


        if (array_key_exists($foreignIdMatcher, $this->availableCompositionFields)) {
            $key = $fieldName;
        }

        if (in_array($foreignIdMatcher, $this->availableCompositionFields)) {
            $key = $fieldName;
        }

        if (!$key) return null;
        return $schema->getCompositionFieldComposingThisField($key);
    }

    public function getFieldPublicName(AbstractField $field): ?string
    {
        $fieldName = $field->getName();
        if (array_key_exists($fieldName, $this->availableFields)) return $fieldName;
        if ($this->includesField($field)) return $this->availableFields[$fieldName];
        return null;
    }
}