<?php

namespace Lkt\Factory\Schemas\Fields;

use Lkt\Factory\Fields\Interfaces\Field;
use Lkt\Factory\Fields\Traits\BaseFieldTrait;
use Lkt\Factory\Fields\Traits\FieldWithComponentOptionTrait;
use Lkt\Factory\Fields\Traits\FieldWithDefaultValue;
use Lkt\Factory\Fields\Traits\FieldWithOrderOptionTrait;
use Lkt\Factory\Fields\Traits\FieldWithPivotOptionTrait;
use Lkt\Factory\Fields\Traits\FieldWithRelatedAccessPolicyOptionTrait;
use Lkt\Factory\Fields\Traits\FieldWithWhereOptionTrait;
use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Schemas\InstanceSettings;
use Lkt\Factory\Schemas\Schema;

class PivotField implements Field
{
    use BaseFieldTrait,
        FieldWithDefaultValue,
        FieldWithComponentOptionTrait,
        FieldWithWhereOptionTrait,
        FieldWithOrderOptionTrait,
        FieldWithPivotOptionTrait,
        FieldWithRelatedAccessPolicyOptionTrait;

    protected Schema|null $pivotSchema = null;

    /**
     * @deprecated
     */
    public static function defineRelation(string $component, string $name, string $column = ''): static
    {
        return (new static($name, $column))->setComponent($component);
    }

    public static function definePivot(
        string $component,
        string $pivotTable,
        string $name,
        string $column = '',
        string|null $schemaComponent = null,
    ): static
    {
        $r = (new static($name, $column));
        $r->component = $component;
        if (!$schemaComponent) {
            $schemaName = ['pivot', $name];
            if ($column) $schemaName[] = $column;
            $schemaName[] = $component;
            $schemaComponent = implode('-', $schemaName);
        }
        $r->pivotSchema = Schema::pivotTable($pivotTable, $schemaComponent)->register();

        return $r;
    }

    public function getPivotSchema(): Schema
    {
        if ($this->pivotSchema !== null) return $this->pivotSchema;
        return Schema::get($this->getPivotComponent());
    }

    final public function getPivotComponent(): string
    {
        if ($this->pivotSchema !== null) return $this->pivotSchema->getComponent();
        return $this->pivotComponent;
    }

    public function setPivotInstanceConfig($class, $generatedInstanceNamespace, $generatedStorageDir): static
    {
        $this->pivotSchema->setInstanceSettings(
            InstanceSettings::define($class)
                ->setNamespaceForGeneratedClass($generatedInstanceNamespace)
                ->setWhereStoreGeneratedClass($generatedStorageDir)
        );
        return $this;
    }

    public function setPivotInstanceSettings(InstanceSettings $settings): static
    {
        $this->pivotSchema->setInstanceSettings($settings);
        return $this;
    }

    public function setFields(array $fields): static
    {
        $this->pivotSchema->setFields($fields);
        return $this;
    }

    /**
     * @deprecated
     */
    public function setPivotLeftIdField(IntegerField $field): static
    {
        $this->pivotSchema->addField($field);
        return $this;
    }

    /**
     * @deprecated
     */
    public function setPivotRightIdField(IntegerField $field): static
    {
        $this->pivotSchema->addField($field);
        return $this;
    }

    /**
     * @deprecated
     */
    public function setPivotPositionField(IntegerField $field): static
    {
        $this->pivotSchema->addField($field);
        return $this;
    }

    public function getTargetComponent(Schema|null $schema = null, Item|null $item = null): string|null
    {
        $pivotSchema = $this->getPivotSchema();

        $pivotIdentifiers = $pivotSchema->getIdentifiers();
        $pivotForeignColumn = null;
        foreach ($pivotIdentifiers as $identifier) {
            if ($identifier instanceof IntegerField && $identifier->isPivot()) {
                if ($identifier->getComponent() === $this->getComponent($schema, $item)) {
                    $pivotForeignColumn = $identifier;
                    break;
                }
            }
        };

        return $pivotForeignColumn?->getComponent();
    }
}