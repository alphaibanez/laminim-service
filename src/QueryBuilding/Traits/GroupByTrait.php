<?php

namespace Lkt\QueryBuilding\Traits;

use Lkt\Connectors\Interfaces\DatabaseConnector;
use Lkt\QueryBuilding\GroupBy;

trait GroupByTrait
{
    protected array $groupBy = [];

    final public function groupBy(GroupBy|string|array $groupBy): static
    {
        if (is_array($groupBy)) {
            $this->groupBy = array_merge($this->groupBy, $groupBy);

        } elseif(is_string($groupBy)) {
            $groupBy = trim($groupBy);
            if ($groupBy !== '') $this->groupBy[] = $groupBy;

        }elseif($groupBy instanceof GroupBy) {
            $this->groupBy[] = $groupBy;
        }
        return $this;
    }

    public function hasGroupBy(): bool
    {
        return count($this->groupBy) > 0;
    }

    public function getGroupBy(DatabaseConnector|null $connector = null): string
    {
        if (!$this->hasGroupBy()) return '';
        $r = [];
        foreach ($this->groupBy as $value) {
            if ($value instanceof GroupBy) {
                $r[] = $value->toString($connector);
            } else {
                $r[] = $value;
            }
        }

        return implode(',', $r);
    }
}