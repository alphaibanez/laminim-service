<?php

namespace Lkt\QueryBuilding;

use Lkt\Connectors\DatabaseConnections;
use Lkt\QueryBuilding\Constraints\SubQueryCountEqualConstraint;
use Lkt\QueryBuilding\Traits\GroupByTrait;
use Lkt\QueryBuilding\Traits\OrderByTrait;
use Lkt\QueryBuilding\Traits\PaginationTrait;
use Lkt\QueryBuilding\Traits\WhereConstraints;

class QueryUnion
{
    use PaginationTrait,
        WhereConstraints,
        OrderByTrait,
        GroupByTrait;

    protected array $builders = [];

    protected string $table = 't';
    protected string $select = '*';

    public static function getEmpty(): static
    {
        return new static();
    }

    public function addQuery(Query $query): static
    {
        $this->builders[] = $query;
        return $this;
    }

    final public function setTable(string $table = 't'): static
    {
        $this->table = $table;
        return $this;
    }

    final public function countMode(): static
    {
        $this->select = 'COUNT(*) as Total';
        return $this;
    }

    final public function selectMode(): static
    {
        $this->select = '*';
        return $this;
    }

    final public function toString(): string
    {
        $queries = [];
        foreach ($this->builders as $builder) {
            $queries[] = $builder->getSelectDistinctQuery();
        }

        $query = '(' . implode(') UNION (', $queries) . ')';

        $whereString = $this->getQueryWhere();
        $query = "SELECT {$this->select} FROM ($query) as {$this->table} {$whereString}";

        if ($this->hasOrder()) {
            $query .= " ORDER BY {$this->getOrder()}";
        }
        $pagination = $this->getPaginationString();

        $query .= " {$pagination}";

        return $query;
    }


    const COMPONENT = null;

    protected string $connector = '';
    protected bool $forceRefresh = false;

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
        return $connection->query($this->toString());
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
}