<?php

namespace Lkt\Instances;

use Lkt\Generated\GeneratedLktCountry;

class LktCountry extends GeneratedLktCountry
{
    const COMPONENT = 'lkt-country';

    public static function getByISOAlpha2(string $code): static|null
    {
        $query = static::getQueryBuilder();
        $query->andIsoCodeAlpha2Equal($code);
        return static::getOne($query);
    }
}