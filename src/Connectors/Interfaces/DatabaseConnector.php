<?php

namespace Lkt\Connectors\Interfaces;

use Lkt\Factory\Instantiator\Enums\DatabaseInsertMode;
use Lkt\Factory\Schemas\Schema;
use Lkt\QueryBuilding\Constraints\AbstractConstraint;
use Lkt\QueryBuilding\Query;

interface DatabaseConnector
{
    public function connect(): self;

    public function disconnect(): self;

    public function query(string $query, array $replacements = []): ?array;

    public function extractSchemaColumns(Schema $schema): array;

    public function getLastInsertedId(): int;

    public function makeUpdateParams(array $params = [], string $type = 'create'): string;

    public function getQuery(Query $builder, string $type, string $countableField = null): string;

    public function prepareDataToStore(Schema $schema, array $data): array;

    public function batchInsert(array $items, Query $builder, Schema $schema, DatabaseInsertMode $mode = DatabaseInsertMode::onDuplicatedIgnore): static;

    public function batchDrop(array $items, Query $builder, Schema $schema): static;


    public function getName(): string;

    public function setHost(string $host): static;

    public function setCharset(string $charset): static;

    public function setDatabase(string $database): static;

    public function getCharset(): string;

    public function getDatabase(): string;

    public function getHost(): string;

    public function getPassword(): string;

    public function getPort(): int;

    public function getUser(): string;

    public function setName(string $name): static;

    public function setPassword(string $password): static;

    public function setPort(int $port): static;

    public function setUser(string $user): static;

    public function forceRefreshNextQuery(): static;

    public function escapeDatabaseCharacters(string $str): string;

    public function unEscapeDatabaseCharacters(string $value): string;

    public function getSelectQuery(Query $builder): string;

    public function getSelectDistinctQuery(Query $builder): string;

    public function getCountQuery(Query $builder, string $countableField): string;

    public function getInsertQuery(Query $builder, DatabaseInsertMode $mode = DatabaseInsertMode::onDuplicatedIgnore): string;

    public function getUpdateQuery(Query $builder): string;

    public function getDeleteQuery(Query $builder): string;

    public function prepareWhereConstraint(AbstractConstraint $whereConstraint): AbstractConstraint;
}