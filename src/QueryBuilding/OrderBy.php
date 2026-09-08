<?php

namespace Lkt\QueryBuilding;

class OrderBy
{
    protected array $data = [];
    protected string $component = '';

    public function __construct(string $field, string $component, bool $asc = true)
    {
        $this->data[] = [$field, $asc];
        $this->component = $component;
    }

    public static function ASC(string $field, string $component = ''): static
    {
        return new static($field, $component, true);
    }

    public static function DESC(string $field, string $component = ''): static
    {
        return new static($field, $component, false);
    }

    public function toString(): string
    {
        $r = [];
        foreach ($this->data as $datum) {
            $r1 = [$datum[0]];

            if ($datum[1]) {
                $r1[] = 'ASC';
            } else {
                $r1[] = 'DESC';
            }
            $r[] = implode(' ', $r1);
        }

        return implode(', ', $r);
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function andASC(string $field): static
    {
        $this->data[] = [$field, true];
        return $this;
    }

    public function andDESC(string $field): static
    {
        $this->data[] = [$field, false];
        return $this;
    }
}