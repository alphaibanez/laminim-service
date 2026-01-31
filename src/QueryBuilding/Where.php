<?php

namespace Lkt\QueryBuilding;

use Lkt\Factory\Schemas\Schema;
use Lkt\QueryBuilding\Traits\WhereConstraints;
use Lkt\QueryBuilding\Traits\WhereStaticConstraints;

class Where
{
    use WhereConstraints,
        WhereStaticConstraints;

    const COMPONENT = '';

    protected $table = '';
    protected $tableAlias = '';

    final public function hasJoinedBuilders(): bool
    {
        return false;
    }

    public function setTable(string $value)
    {
        $this->table = $value;
        return $this;
    }

    public function setTableAlias(string $value)
    {
        $this->tableAlias = $value;
        return $this;
    }

    public function getTable(): string
    {
        if ($this->table) {
            return $this->table;
        }

        $r = '';
        if (static::COMPONENT) {

            try {
                $schema = Schema::get(static::COMPONENT);
                $r = $schema->getTable();

            } catch (\Exception $e) {
                $r = '';
            }
        }

        return $r;
    }

    public function getTableAlias(): string
    {
        return $this->tableAlias;
    }

    /**
     * @deprecated
     * @return static
     */
    public static function getInstance(): self
    {
        return new static();
    }

    /**
     * @return static
     */
    public static function getEmpty()
    {
        return new static();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->whereConstraintsToString();
    }

    /**
     * @param Query $builder
     * @param $value
     * @return static
     */
    public static function subQueryEqual(Query $builder, $value): self
    {
        return (new static())->andSubQueryEqual($builder, $value);
    }

    /**
     * @param Query $builder
     * @param $value
     * @return static
     */
    public static function subQueryIn(Query $builder, $value): self
    {
        return (new static())->andSubQueryIn($builder, $value);
    }

    /**
     * @param Query $builder
     * @param $value
     * @return static
     */
    public static function columnInSubQuery($value, Query $builder): self
    {
        return (new static())->andColumnInSubQuery($builder, $value);
    }

    /**
     * @param Where $builder
     * @return $this
     */
    public function orWhere(Where|callable $builder): self
    {
        $this->or[] = $builder;
        return $this;
    }

    /**
     * @param Where $builder
     * @return $this
     */
    public function andWhere(Where|callable $builder): self
    {
        $this->and[] = $builder;
        return $this;
    }
}