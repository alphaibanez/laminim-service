<?php

namespace Lkt\Instances;

use Lkt\Generated\GeneratedLktDateFormat;

class LktDateFormat extends GeneratedLktDateFormat
{
    const COMPONENT = 'lkt-date-format';

    public static function createOrUpdate(array $data): static
    {
        $instance = static::getOne(static::getUniqueFilteredQueryBuilder($data));
        if (!$instance) {
            $instance = static::getInstance()->autoCreate($data);
        } else {
            $instance->autoUpdate($data);
        }
        return $instance;
    }

    public static function createIfMissing(array $data): static
    {
        $instance = static::getOne(static::getUniqueFilteredQueryBuilder($data));
        if (!$instance) {
            $instance = static::getInstance()->autoCreate($data);
        }
        return $instance;
    }
}