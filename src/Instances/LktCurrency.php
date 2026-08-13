<?php

namespace Lkt\Instances;

use Lkt\Generated\GeneratedLktCurrency;

class LktCurrency extends GeneratedLktCurrency
{
    const COMPONENT = 'lkt-currency';

    public static function getByISOAlpha3(string $code): static|null
    {
        $query = static::getQueryBuilder();
        $query->andIsoCodeAlpha3Equal($code);
        return static::getOne($query);
    }
}