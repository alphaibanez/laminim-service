<?php

namespace Lkt\Factory\Schemas\ValueObjects;

use Lkt\QueryBuilding\Query;

class ItemToI18nPolicy
{
    public readonly mixed $queryBuilderTweak;

    public function __construct(
        public readonly string $i18nKey,
        public readonly string $valueField,
        public readonly string $labelField,
        callable|null $queryBuilderTweak = null)
    {
        $this->queryBuilderTweak = $queryBuilderTweak;
    }

    public function tweakQueryBuilder(Query &$query): void
    {
        if (is_callable($this->queryBuilderTweak)) {
            call_user_func_array($this->queryBuilderTweak, ['query' => $query]);
        }
    }
}