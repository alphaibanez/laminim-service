<?php

namespace Lkt\QueryBuilding\Constraints;

abstract class AbstractConcatConstraint extends AbstractConstraint
{
    protected array $columns;
    protected string $separator;

    public static function defineConcat(array $columns, string $separator, $value = null): static
    {
        $r = parent::define('', $value, []);
        $r->columns = $columns;
        $r->separator = $separator;
        return $r;
    }


    protected function getConcatBuild(): string
    {
        $prepend = $this->getTablePrepend();


        $column = [];
        foreach ($this->columns as $c) {
            $column[] = $prepend.$c;
            $column[] = $this->separator;
        };
        if (isset($column[0])) unset($column[count($column) - 1]);
        $column = implode(',', $column);
        $column = rtrim($column, $this->separator);
        if ($column !== '') {
            $column = "CONCAT({$column})";
        }

        return $column;
    }
}