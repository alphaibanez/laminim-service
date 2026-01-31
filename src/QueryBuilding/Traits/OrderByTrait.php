<?php

namespace Lkt\QueryBuilding\Traits;

use Lkt\QueryBuilding\OrderBy;

trait OrderByTrait
{
    protected array $orderBy = [];

    final public function orderBy(OrderBy|string|array $orderBy): static
    {
        if (is_array($orderBy)) {
            $this->orderBy = array_merge($this->orderBy, $orderBy);

        } elseif(is_string($orderBy)) {
            $orderBy = trim($orderBy);
            if ($orderBy !== '') $this->orderBy[] = $orderBy;

        }elseif($orderBy instanceof OrderBy) {
            $this->orderBy[] = $orderBy;
        }
        return $this;
    }

    public function hasOrder(): bool
    {
        return count($this->orderBy) > 0;
    }

    public function getOrder(): string
    {
        if (!$this->hasOrder()) return '';
        $r = [];
        foreach ($this->orderBy as $value) {
            if ($value instanceof OrderBy) {
                $r[] = $value->toString();
            } else {
                $r[] = $value;
            }
        }

        return implode(',', $r);
    }
}