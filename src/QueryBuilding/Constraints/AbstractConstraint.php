<?php

namespace Lkt\QueryBuilding\Constraints;

abstract class AbstractConstraint
{
    protected string $column = '';
    protected string $table = '';
    protected string $tableAlias = '';
    protected $value = null;
    protected array $settings = [];

    public function __construct(string $column, $value = null, array $settings = [])
    {
        $this->column = $column;
        $this->value = $value;
        $this->settings = $settings;
    }

    public static function define(string $column, $value = null, array $settings = []): static
    {
        return new static($column, $value, $settings);
    }

    abstract public function __toString(): string;

    public function setTable(string $table, string $alias = ''): static
    {
        $this->table = $table;
        $this->tableAlias = $alias;
        return $this;
    }

    protected function getTablePrepend(): string
    {
        if (strpos($this->column, 'JSON_') === 0) return '';
        $alias = trim($this->tableAlias);
        if ($alias !== '') return "{$alias}.";

        $table = trim($this->table);
        if ($table !== '') return "{$table}.";
        return '';
    }

    public function getColumn(): string
    {
        return $this->column;
    }

    public function setColumn(string $column): static
    {
        $this->column = $column;
        return $this;
    }

    public function getTable(): string
    {
        return $this->table;
    }
}