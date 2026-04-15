<?php

namespace Lkt\Factory\Instantiator\Instances;

use Lkt\Factory\Instantiator\Enums\BatchInsertMode;
use Lkt\Factory\Instantiator\ValueObjects\ComponentDatabaseIntegration;
use Lkt\Factory\Schemas\Fields\AbstractField;
use Lkt\Factory\Schemas\Schema;

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

    public function insert(BatchInsertMode $mode = BatchInsertMode::onDuplicatedIgnore): void
    {
        if (count($this->items) === 0) return;

        $dbIntegration = ComponentDatabaseIntegration::from($this->schema->getComponent());
        $builder = $dbIntegration->query;
        $connection = $dbIntegration->databaseConnector;

        $connection->batchInsert($this->items, $builder, $this->schema, $mode);
    }
}