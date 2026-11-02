<?php

namespace Lkt\Instances;

use Lkt\Factory\Schemas\Schema;
use Lkt\Generated\GeneratedLktCurrency;

class LktCurrency extends GeneratedLktCurrency
{
    const COMPONENT = 'lkt-currency';

    public static function getByISOAlpha3(string $code): static|null
    {
        $schema = Schema::get(static::COMPONENT);
        $query = $schema->getQueryBuilder();
        $query->andStringEqual('iso_code_alpha3', $code);
        return static::getOne($query);
    }
}