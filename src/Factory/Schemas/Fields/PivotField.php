<?php

namespace Lkt\Factory\Schemas\Fields;

use Lkt\Factory\Schemas\InstanceSettings;
use Lkt\Factory\Schemas\Schema;
use Lkt\Factory\Schemas\Traits\FieldWithComponentOptionTrait;
use Lkt\Factory\Schemas\Traits\FieldWithOrderOptionTrait;
use Lkt\Factory\Schemas\Traits\FieldWithPivotOptionTrait;
use Lkt\Factory\Schemas\Traits\FieldWithRelatedAccessPolicyOptionTrait;
use Lkt\Factory\Schemas\Traits\FieldWithWhereOptionTrait;
use Lkt\Factory\Schemas\Values\ComponentValue;

class PivotField extends AbstractField
{
    use FieldWithComponentOptionTrait,
        FieldWithWhereOptionTrait,
        FieldWithOrderOptionTrait,
        FieldWithPivotOptionTrait,
        FieldWithRelatedAccessPolicyOptionTrait;

    protected Schema|null $pivotSchema = null;

    public static function defineRelation(string $component, string $name, string $column = ''): static
    {
        return (new static($name, $column))->setComponent($component);
    }

    public static function definePivot(
        string $component,
        string $pivotTable,
        string $name,
        string $column = '',
    ): static
    {
        $r = (new static($name, $column))->setComponent($component);
        $schemaName = ['pivot', $name];
        if ($column) $schemaName[] = $column;
        $schemaName[] = $component;
        $r->pivotSchema = Schema::pivotTable($pivotTable, implode('-', $schemaName))->register();

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

        if ($this->pivotComponent instanceof ComponentValue) {
            return $this->pivotComponent->getValue();
        }
        return '';
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

    public function setPivotLeftIdField(PivotLeftIdField $field): static
    {
        $this->pivotSchema->addField($field);
        return $this;
    }

    public function setPivotRightIdField(PivotRightIdField $field): static
    {
        $this->pivotSchema->addField($field);
        return $this;
    }

    public function setPivotPositionField(PivotPositionField $field): static
    {
        $this->pivotSchema->addField($field);
        return $this;
    }

    public function getQueryBuilderGetter(): string
    {
        return $this->getGetterForPrimitiveValue() . 'QueryBuilder';
    }
}