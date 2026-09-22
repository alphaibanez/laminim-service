<?php

namespace Lkt\Connectors;

use Lkt\Connectors\Traits\Database\BaseDatabaseConnector;
use Lkt\Factory\Instantiator\Enums\DatabaseInsertMode;
use Lkt\Factory\Schemas\Schema;
use Lkt\QueryBuilding\Query;

/**
 * @deprecated
 */
abstract class AbstractDatabaseConnector
{
    use BaseDatabaseConnector;

    abstract public function connect(): self;
    abstract public function disconnect(): self;
    abstract public function query(string $query, array $replacements = []):? array;
    abstract public function extractSchemaColumns(Schema $schema): array;
    abstract public function getLastInsertedId(): int;
    abstract public function makeUpdateParams(array $params = [], string $type = 'create') :string;
    abstract public function getQuery(Query $builder, string $type, string $countableField = null): string;
    abstract public function prepareDataToStore(Schema $schema, array $data): array;
    abstract public function batchInsert(array $items, Query $builder, Schema $schema, DatabaseInsertMode $mode = DatabaseInsertMode::onDuplicatedIgnore): static;
    abstract public function batchDrop(array $items, Query $builder, Schema $schema): static;


}