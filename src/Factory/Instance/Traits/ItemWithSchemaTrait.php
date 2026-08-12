<?php

namespace Lkt\Factory\Instance\Traits;

use Lkt\Factory\Instantiator\Helpers\QueryBuilderHelper;
use Lkt\Factory\Schemas\Exceptions\SchemaNotDefinedException;
use Lkt\Factory\Schemas\Schema;
use Lkt\QueryBuilding\Query;

trait ItemWithSchemaTrait
{
    protected Schema $schema;

    public function getSchema(): Schema|null
    {
        return Schema::get(static::COMPONENT);
    }

    /**
     * @return Query
     * @throws SchemaNotDefinedException
     */
    public static function getQueryBuilder()
    {
        return QueryBuilderHelper::getComponentQuery(static::COMPONENT);
    }
}