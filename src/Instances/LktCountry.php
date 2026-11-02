<?php

namespace Lkt\Instances;

use Lkt\Factory\Schemas\Schema;
use Lkt\Generated\GeneratedLktCountry;

class LktCountry extends GeneratedLktCountry
{
    const COMPONENT = 'lkt-country';

    public static function getByISOAlpha2(string $code): static|null
    {
        $schema = Schema::get(static::COMPONENT);
        $query = $schema->getQueryBuilder();
        $query->andStringEqual('iso_code_alpha2', $code);
        return static::getOne($query);
    }
}