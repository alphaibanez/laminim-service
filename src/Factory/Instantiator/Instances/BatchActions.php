<?php

namespace Lkt\Factory\Instantiator\Instances;

use Lkt\Factory\Instantiator\Enums\BatchInsertMode;
use Lkt\Factory\Instantiator\Instantiator;
use Lkt\Factory\Instantiator\ValueObjects\ComponentDatabaseIntegration;
use Lkt\Factory\Schemas\Enums\AccessPolicyEndOfLife;
use Lkt\Factory\Schemas\Fields\ForeignKeyField;
use Lkt\Factory\Schemas\Fields\ForeignKeysField;
use Lkt\Factory\Schemas\Schema;
use Lkt\Factory\Schemas\ValueObjects\AccessPolicy;

class BatchActions
{
    /** @var AbstractInstance[]  */
    public readonly array $items;

    /**
     * @param Schema $schema
     * @param AbstractInstance[] $items
     */
    protected function __construct(public readonly Schema $schema, array $items)
    {
        $this->items = array_values($items);
    }

    public static function fromSchema(Schema $schema, array $items): static
    {
        return new static($schema, $items);
    }

    public static function fromComponent(string $component, array $items): static
    {
        return new static(Schema::get($component), $items);
    }

    public function create(BatchInsertMode $mode = BatchInsertMode::onDuplicatedIgnore): void
    {
        if (count($this->items) === 0) return;

        $dbIntegration = ComponentDatabaseIntegration::from($this->schema->getComponent());
        $builder = $dbIntegration->query;
        $connection = $dbIntegration->databaseConnector;

        $connection->batchInsert($this->items, $builder, $this->schema, $mode);
    }

    public function update(): void
    {
        if (count($this->items) === 0) return;

        $dbIntegration = ComponentDatabaseIntegration::from($this->schema->getComponent());
        $connection = $dbIntegration->databaseConnector;

        $connection->batchUpdate($this->items, $this->schema);
    }

    public function drop(): void
    {
        if (count($this->items) === 0) return;

        $dbIntegration = ComponentDatabaseIntegration::from($this->schema->getComponent());
        $builder = $dbIntegration->query;
        $connection = $dbIntegration->databaseConnector;

        $connection->batchDrop($this->items, $builder, $this->schema);
    }

    public function read(string|null|AccessPolicy $accessPolicyName = '', string $mode = ''): array
    {
        if (count($this->items) === 0) return [];

        $preFetchForeignKey = [];
        $preFetchForeignKeys = [];
//        $preFetchRelatedKeys = [];

        if ($accessPolicyName instanceof AccessPolicy) $accessPolicyName = $accessPolicyName->name;

        $accessPolicy = $accessPolicyName ? $this->schema->getAccessPolicy($accessPolicyName) : null;
        $fields = $this->schema->getRelationalFields();

        foreach ($fields as $field) {
            if (!$accessPolicy || $accessPolicy->includesField($field)) {
                if ($field instanceof ForeignKeyField) {
                    $component = $field->getComponent();
                    if (!$component) continue;

                    $getter = $field->getGetterForPrimitiveValue();

                    foreach ($this->items as $item) {
                        $v = $item->{$getter}();
                        if (!isset($preFetchForeignKey[$component])) $preFetchForeignKey[$component] = [];
                        if (!in_array($v, $preFetchForeignKey[$component])) {
                            $preFetchForeignKey[$component][] = $v;
                        }
                    }
                }
                elseif ($field instanceof ForeignKeysField) {
                    $component = $field->getComponent();
                    if (!$component) continue;

                    $getter = $field->getGetterForPrimitiveValue();

                    foreach ($this->items as $item) {
                        $v = $item->{$getter}();
                        if (!isset($preFetchForeignKeys[$component])) $preFetchForeignKeys[$component] = [];
                        foreach ($v as $v_) {
                            if (!in_array($v_, $preFetchForeignKeys[$component])) {
                                $preFetchForeignKeys[$component][] = $v_;
                            }
                        }
                    }
                }
//                elseif ($field instanceof RelatedKeysField) {
//                    $component = $field->getComponent();
//                    if (!$component) continue;
//
//                    $getter = $this->schema->getIdentifiers()[0]->getGetterForPrimitiveValue();
//
//                    foreach ($this->items as $item) {
//                        $v = $item->{$getter}();
//                        if (!isset($preFetchRelatedKeys[$component])) $preFetchRelatedKeys[$component] = [];
//                        if (!in_array($v_, $preFetchRelatedKeys[$component])) {
//                            $preFetchRelatedKeys[$component][] = $v;
//                        }
//                    }
//                }
            }
        }

        $components = array_unique([...array_keys($preFetchForeignKey), ...array_keys($preFetchForeignKeys)]);
        foreach ($components as $component) {
            if (!$component) continue;

            $values = array_unique([...$preFetchForeignKey[$component] ?? [], ...$preFetchForeignKeys[$component] ?? []]);

            $relatedSchema = Schema::get($component);
            $dbIntegration = ComponentDatabaseIntegration::from($component);
            $builder = $dbIntegration->query;

            $identifiers = $relatedSchema->getIdentifiers();
            if (count($identifiers) === 1) {
                $relatedSchema->filterBuilder($builder, [$identifiers[0]->getName() => $values]);
                Instantiator::makeResults($component, $builder->selectDistinct());
            }
        }

        $r = [];
        foreach ($this->items as $item) {
            if ($accessPolicyName) {
                $item->setAccessPolicy($accessPolicyName, AccessPolicyEndOfLife::UntilNextRead);
            }
            if ($mode === 'related') {
                $r[] = $item->autoRead();
            } else {
                $r[] = $item->autoRead();
            }
        }
        return $r;
    }
}