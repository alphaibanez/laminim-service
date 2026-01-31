<?php

namespace Lkt\QueryBuilding;

class GroupBy
{
    protected array $data = [];

    public function __construct(string $field)
    {
        $this->data[] = $field;
    }

    public function toString(): string
    {
        $r = [];
        foreach ($this->data as $datum) {
            $r1 = [$datum];
            $r[] = implode(' ', $r1);
        }

        return implode(', ', $r);
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function andGroup(string $field): static
    {
        $this->data[] = $field;
        return $this;
    }

    public static function define(string $field): static
    {
        return new static($field);
    }
}