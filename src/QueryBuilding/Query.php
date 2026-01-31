<?php

namespace Lkt\QueryBuilding;

use Lkt\Connectors\DatabaseConnections;
use Lkt\Factory\Schemas\Schema;
use Lkt\QueryBuilding\Constraints\FieldInSubQueryConstraint;
use Lkt\QueryBuilding\Constraints\FieldNotInSubQueryConstraint;
use Lkt\QueryBuilding\Constraints\SubQueryCountEqualConstraint;
use Lkt\QueryBuilding\Enums\JoinType;
use Lkt\QueryBuilding\Traits\GroupByTrait;
use Lkt\QueryBuilding\Traits\OrderByTrait;
use Lkt\QueryBuilding\Traits\PaginationTrait;
use Lkt\QueryBuilding\Traits\WhereConstraints;
use function Lkt\Tools\Pagination\getTotalPages;

class Query
{
    use WhereConstraints,
        PaginationTrait,
        OrderByTrait,
        GroupByTrait;


    const COMPONENT = null;

    protected string $connector = '';
    protected bool $forceRefresh = false;

    protected SelectBuilder|array $columns = [];
    protected string $table = '';
    protected string $tableAlias = '';
    protected array $where = [];
    protected array $data = [];

    protected array $joins = [];
    protected array $unions = [];

    protected array $joinedBuilders = [];
    protected array $joinedBuildersRelation = [];

    final public function leftJoin(Query $query, $joinedBuilderColumn, $thisBuilderColumn): static
    {
        $k = $query->getJoinKey();
        $this->joinedBuilders[$k] = $query;
        $this->joinedBuildersRelation[$k] = [JoinType::left, $joinedBuilderColumn, $thisBuilderColumn];
        return $this;
    }

    final public function rightJoin(Query $query, $joinedBuilderColumn, $thisBuilderColumn): static
    {
        $k = $query->getJoinKey();
        $this->joinedBuilders[$k] = $query;
        $this->joinedBuildersRelation[$k] = [JoinType::right, $joinedBuilderColumn, $thisBuilderColumn];
        return $this;
    }

    /**
     * @deprecated
     */
    final public function union(Query $builder, string $alias): static
    {
        $this->unions[$alias] = $builder;
        return $this;
    }

    final public function setColumns(SelectBuilder|array $columns): static
    {
        $this->columns = $columns;
        return $this;
    }

    final protected function setTable(string $table): static
    {
        $this->table = $table;
        return $this;
    }

    final public function setTableAlias(string $alias): static
    {
        $this->tableAlias = $alias;
        return $this;
    }

    public static function table(string $table): static
    {
        return (new static())->setTable($table);
    }

    final public function updateData(array $data): static
    {
        $this->data = $data;
        return $this;
    }

    final public function where(Where $where): static
    {
        $this->and[] = $where;
        return $this;
    }

    final public function constraint(string $where): static
    {
        $this->constraints[] = $where;
        return $this;
    }

    final public function join(Join $join): static
    {
        $this->joins[] = $join;
        return $this;
    }

    public function getTable(): string
    {
        return $this->table;
    }

    public function getTableAlias(): string
    {
        return $this->tableAlias;
    }

    public function hasTableAlias(): bool
    {
        return $this->tableAlias !== '';
    }

    public function getJoinKey(): string
    {
        if ($this->hasTableAlias()) {
            return $this->getTableAlias();
        }
        return $this->getTable();
    }

    public function hasJoinedBuilders(): bool
    {
        return count($this->joinedBuilders) > 0;
    }

    /**
     * @return static[]
     */
    public function getJoinedBuilders(): array
    {
        return $this->joinedBuilders;
    }

    public function getJoinedBuildersRelations(): array
    {
        return $this->joinedBuildersRelation;
    }

    public function getJoinedBuildersRelation(string $key): ?array
    {
        return $this->joinedBuildersRelation[$key];
    }

    public function getTableNameOrAlias(): string
    {
        if ($this->hasTableAlias()) {
            return $this->getTableAlias();
        }
        return $this->getTable();
    }

    public function getTableNameWithAlias(): string
    {
        if ($this->hasTableAlias()) {
            return "{$this->getTable()} AS {$this->getTableAlias()}";
        }
        return $this->getTable();
    }

    public function getJoinString(string $joinType, $joinedColumn, $otherBuilderColumn): string
    {
        $additional = '';
        if ($this->hasJoinedBuilders()) {
            foreach ($this->getJoinedBuilders() as $key => $joinedBuilder) {
                $joinData = $this->getJoinedBuildersRelation($key);
                $additional .= $joinedBuilder->getJoinString($joinData[0], $joinData[1], $this->formatJoinedColumn($joinData[2]));
            }
        }

        $_joinType = strtoupper($joinType);
        $joinedColumn = $this->formatJoinedColumn($joinedColumn);
        $table = $this->getTableNameWithAlias();
        return "{$_joinType} JOIN {$table} ON ({$joinedColumn} = {$otherBuilderColumn}) {$additional}";
    }

    public function formatJoinedColumn($joinedColumn)
    {
        if (is_string($joinedColumn)) {
            $table = $this->getTableNameOrAlias();
            return "{$table}.{$joinedColumn}";
        }
        return $joinedColumn;
    }

    public function getJoins(): array
    {
        return $this->joins;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getColumns(): array
    {
        if ($this->columns instanceof SelectBuilder) return $this->columns->getFields();
        return $this->columns;
    }

    /**
     * @param bool $status
     * @return $this
     */
    public function setForceRefresh(bool $status): static
    {
        $this->forceRefresh= $status;
        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setDatabaseConnector(string $name): static
    {
        $this->connector = $name;
        return $this;
    }

    final public function select(): array
    {
        $connector = $this->connector;
        if ($connector === '') {
            $connector = DatabaseConnections::$defaultConnector;
        }
        $connection = DatabaseConnections::get($connector);
        if ($this->forceRefresh) {
            $connection->forceRefreshNextQuery();
        }
        $r = $connection->query($this->getSelectQuery());

        if (!is_array($r)) {
            $r = [];
        }
        return $r;
    }

    final public function selectRaw(): array
    {
        $connector = $this->connector;
        if ($connector === '') {
            $connector = DatabaseConnections::$defaultConnector;
        }
        $connection = DatabaseConnections::get($connector);
        if ($this->forceRefresh) {
            $connection->forceRefreshNextQuery();
        }
        $r = $connection->query($this->getSelectQuery());

        if (!is_array($r)) {
            $r = [];
        }
        return $r;
    }

    final public function selectDistinct(): array
    {
        $connector = $this->connector;
        if ($connector === '') {
            $connector = DatabaseConnections::$defaultConnector;
        }
        $connection = DatabaseConnections::get($connector);
        if ($this->forceRefresh) {
            $connection->forceRefreshNextQuery();
        }
        return $connection->query($this->getSelectDistinctQuery());
    }

    final public function selectDistinctRaw(): array
    {
        $connector = $this->connector;
        if ($connector === '') {
            $connector = DatabaseConnections::$defaultConnector;
        }
        $connection = DatabaseConnections::get($connector);
        if ($this->forceRefresh) {
            $connection->forceRefreshNextQuery();
        }
        $r = $connection->query($this->getSelectDistinctQuery());

        if (!is_array($r)) {
            $r = [];
        }
        return $r;
    }

    final public function count(string $countableField): int
    {
        $connector = $this->connector;
        if ($connector === '') {
            $connector = DatabaseConnections::$defaultConnector;
        }
        $connection = DatabaseConnections::get($connector);
        if ($this->forceRefresh) {
            $connection->forceRefreshNextQuery();
        }
        $results = $connection->query($this->getCountQuery($countableField));
        return (int)$results[0]['Count'];
    }

    final public function pages(string $countableField): int
    {
        return getTotalPages($this->count($countableField), $this->limit);
    }

    final public function insert(): bool
    {
        $connector = $this->connector;
        if ($connector === '') {
            $connector = DatabaseConnections::$defaultConnector;
        }
        $connection = DatabaseConnections::get($connector);
        if ($this->forceRefresh) {
            $connection->forceRefreshNextQuery();
        }
        $connection->query($this->getInsertQuery());
        return true;
    }

    final public function update(): bool
    {
        $connector = $this->connector;
        if ($connector === '') {
            $connector = DatabaseConnections::$defaultConnector;
        }
        $connection = DatabaseConnections::get($connector);
        if ($this->forceRefresh) {
            $connection->forceRefreshNextQuery();
        }
        $connection->query($this->getUpdateQuery());
        return true;
    }

    final public function delete(): bool
    {
        $connector = $this->connector;
        if ($connector === '') {
            $connector = DatabaseConnections::$defaultConnector;
        }
        $connection = DatabaseConnections::get($connector);
        if ($this->forceRefresh) {
            $connection->forceRefreshNextQuery();
        }
        $connection->query($this->getDeleteQuery());
        return true;
    }

    final public function extractSchemaColumns(Schema $schema): static
    {
        $connector = $this->connector;
        if ($connector === '') {
            $connector = DatabaseConnections::$defaultConnector;
        }
        $connection = DatabaseConnections::get($connector);
        if ($this->forceRefresh) {
            $connection->forceRefreshNextQuery();
        }
        $this->setColumns($connection->extractSchemaColumns($schema));
        return $this;
    }

    final public function getSelectQuery(): string
    {
        $connector = $this->connector;
        if ($connector === '') {
            $connector = DatabaseConnections::$defaultConnector;
        }
        $connection = DatabaseConnections::get($connector);
        return $connection->getSelectQuery($this);
    }

    final public function getSelectDistinctQuery(): string
    {
        $connector = $this->connector;
        if ($connector === '') {
            $connector = DatabaseConnections::$defaultConnector;
        }
        $connection = DatabaseConnections::get($connector);
        return $connection->getSelectDistinctQuery($this);
    }

    final public function getCountQuery(string $countableField): string
    {
        $connector = $this->connector;
        if ($connector === '') {
            $connector = DatabaseConnections::$defaultConnector;
        }
        $connection = DatabaseConnections::get($connector);
        return $connection->getCountQuery($this, $countableField);
    }

    final public function getInsertQuery(): string
    {
        $connector = $this->connector;
        if ($connector === '') {
            $connector = DatabaseConnections::$defaultConnector;
        }
        $connection = DatabaseConnections::get($connector);
        return $connection->getInsertQuery($this);
    }

    final public function getUpdateQuery(): string
    {
        $connector = $this->connector;
        if ($connector === '') {
            $connector = DatabaseConnections::$defaultConnector;
        }
        $connection = DatabaseConnections::get($connector);
        return $connection->getUpdateQuery($this);
    }

    final public function getDeleteQuery(): string
    {
        $connector = $this->connector;
        if ($connector === '') {
            $connector = DatabaseConnections::$defaultConnector;
        }
        $connection = DatabaseConnections::get($connector);
        return $connection->getDeleteQuery($this);
    }

    final public function andSubQueryCountEqual(Query $query, int $value, string $countableField): static
    {
        $this->and[] = SubQueryCountEqualConstraint::define($query->getCountQuery($countableField), $value);
        return $this;
    }

    final public function orSubQueryCountEqual(Query $query, int $value, string $countableField): static
    {
        $this->and[] = SubQueryCountEqualConstraint::define($query->getCountQuery($countableField), $value);
        return $this;
    }

    final public function andFieldInSubQuery(string $value, Query $query): static
    {
        $this->and[] = FieldInSubQueryConstraint::define($value, $query->getSelectDistinctQuery());
        return $this;
    }

    final public function orFieldInSubQuery(string $value, Query $query): static
    {
        $this->and[] = FieldInSubQueryConstraint::define($value, $query->getSelectDistinctQuery());
        return $this;
    }

    final public function andFieldNotInSubQuery(string $value, Query $query): static
    {
        $this->and[] = FieldNotInSubQueryConstraint::define($value, $query->getSelectDistinctQuery());
        return $this;
    }

    final public function orFieldNotInSubQuery(string $value, Query $query): static
    {
        $this->and[] = FieldNotInSubQueryConstraint::define($value, $query->getSelectDistinctQuery());
        return $this;
    }
}