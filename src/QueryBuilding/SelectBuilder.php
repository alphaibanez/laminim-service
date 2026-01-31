<?php

namespace Lkt\QueryBuilding;

class SelectBuilder
{
    protected array $fields = [];

    public function getFields(): array
    {
        return $this->fields;
    }

    public function andDatum(string $column, ?string $as = null): static
    {
        $a = [$column];
        if ($as) $a[] = $as;
        $this->fields[] = implode(' AS ', $a);
        return $this;
    }

    public function andCountDatum(string $column, ?string $as = null): static
    {
        $a = ["COUNT($column)"];
        if ($as) $a[] = $as;
        $this->fields[] = implode(' AS ', $a);
        return $this;
    }

    public function andYearDatum(string $column, ?string $as = null): static
    {
        $a = ["YEAR($column)"];
        if ($as) $a[] = $as;
        $this->fields[] = implode(' AS ', $a);
        return $this;
    }

    public function andMonthDatum(string $column, ?string $as = null): static
    {
        $a = ["MONTH($column)"];
        if ($as) $a[] = $as;
        $this->fields[] = implode(' AS ', $a);
        return $this;
    }

    public function andExtractYearMonthDatum(string $column, ?string $as = null): static
    {
        $a = ["EXTRACT(YEAR_MONTH FROM $column)"];
        if ($as) $a[] = $as;
        $this->fields[] = implode(' AS ', $a);
        return $this;
    }

    public static function datum(string $column, ?string $as = null): static
    {
        $a = [$column];
        if ($as) $a[] = $as;
        $r = new static();
        $r->fields[] = implode(' AS ', $a);
        return $r;
    }

    public static function countDatum(string $column, ?string $as = null): static
    {
        $a = ["COUNT($column)"];
        if ($as) $a[] = $as;
        $r = new static();
        $r->fields[] = implode(' AS ', $a);
        return $r;
    }

    public static function yearDatum(string $column, ?string $as = null): static
    {
        $a = ["YEAR($column)"];
        if ($as) $a[] = $as;
        $r = new static();
        $r->fields[] = implode(' AS ', $a);
        return $r;
    }

    public static function monthDatum(string $column, ?string $as = null): static
    {
        $a = ["MONTH($column)"];
        if ($as) $a[] = $as;
        $r = new static();
        $r->fields[] = implode(' AS ', $a);
        return $r;
    }

    public static function extractYearMonthDatum(string $column, ?string $as = null): static
    {
        $a = ["EXTRACT(YEAR_MONTH FROM `$column`)"];
        if ($as) $a[] = $as;
        $r = new static();
        $r->fields[] = implode(' AS ', $a);
        return $r;
    }
}