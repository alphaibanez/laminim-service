<?php

namespace Lkt\Instances;

use Lkt\Generated\GeneratedLktFileFormat;

class LktFileFormat extends GeneratedLktFileFormat
{
    const COMPONENT = 'lkt-file-format';

    public static function createOrUpdate(array $data): static
    {
        $instance = static::getOne(static::getUniqueFilteredQueryBuilder());
        if (!$instance) {
            $instance = static::getInstance()->autoCreate($data);
        } else {
            $instance->autoUpdate($data);
        }
        return $instance;
    }

    public static function createIfMissing(array $data): static
    {
        $instance = static::getOne(static::getUniqueFilteredQueryBuilder());
        if (!$instance) {
            $instance = static::getInstance()->autoCreate($data);
        }
        return $instance;
    }
}