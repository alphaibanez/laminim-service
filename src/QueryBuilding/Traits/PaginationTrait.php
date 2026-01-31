<?php

namespace Lkt\QueryBuilding\Traits;

trait PaginationTrait
{

    protected int $page = -1;
    protected int $limit = -1;


    final public function pagination(int $page = 0, int $limit = 0): static
    {
        if ($page < 1) $page = 1;
        --$page;
        $this->page = $page;
        if ($limit > 0) $this->limit = $limit;

        if ($this->page >= 0 && $this->limit <= 0) {
            $this->limit = 20;
        }
        return $this;
    }

    final public function andPageIs(int $page = 0): static
    {
        $this->page = $page;
        return $this;
    }

    final public function andPageLimitIs(int $limit = 0): static
    {
        $this->limit = $limit;
        return $this;
    }

    public function hasPagination(): bool
    {
        return $this->page > -1 && $this->limit > -1;
    }

    public function hasLimit(): bool
    {
        return $this->limit > -1;
    }

    protected function getPaginationString(): string
    {
        $r = '';
        if ($this->page > -1 && $this->limit > -1) {
            $p = $this->page * $this->limit;
            $r = " LIMIT {$p}, {$this->limit}";

        } elseif ($this->limit > -1) {
            $r = " LIMIT {$this->limit}";
        }

        return $r;
    }
}