<?php

namespace Lkt\QueryBuilding\Interfaces;

use Lkt\Connectors\Interfaces\DatabaseConnector;

interface QueryConstraint
{
    public static function define(string $column, $value = null, array $settings = []): static;


    public function setTable(string $table, string $alias = ''): static;

    public function getColumn(): string;

    public function setColumn(string $column): static;

    public function setValue(string $value): static;

    public function getTable(): string;

    public function getValue(): mixed;

    public function toString(DatabaseConnector|null $connector = null): string;
}