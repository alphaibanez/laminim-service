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

        $values = [];
        foreach ($this->items as $item) {
            $parsed = $connection->prepareDataToStore($this->schema, $item->getUpdatedData());
            $builder->updateData($parsed);

            $values[] = $connection->makeUpdateParamsArray($builder->getData(), 'create');
        }

        $valuesKeys = '(' . implode(',', array_keys($values[0])) . ')';
        $values = array_map(function (array $v) { return implode(', ', $v); }, $values);
        $valuesString = '(' . implode('),(', $values) . ')';

        $query = $mode === BatchInsertMode::onDuplicatedIgnore ? "INSERT IGNORE INTO" : "INSERT INTO";

        $query .= " {$this->schema->getTable()} $valuesKeys VALUES $valuesString";

        if ($mode === BatchInsertMode::onDuplicatedUpdate) {
            $updateKeys = [];
            $identifiers = array_map(function (AbstractField $f) { return $f->getColumn();}, $this->schema->getIdentifiers());
            $fields = array_map(function (AbstractField $f) { return $f->getColumn();}, $this->schema->getSameTableFields());
            $fields = array_values(array_filter($fields, function (string $f) use ($identifiers) {
                return !in_array($f, $identifiers);
            }));

            foreach ($fields as $field) {
                $updateKeys[] = "{$field} = VALUES({$field})";
            }

            if (count($updateKeys) > 0) {
                $updateKeysStr = implode(', ', $updateKeys);
                $query .= " ON DUPLICATE KEY UPDATE {$updateKeysStr}";
            }
        }

        $connection->query($query);
    }
}