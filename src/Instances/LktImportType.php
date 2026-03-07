<?php

namespace Lkt\Instances;

use Lkt\Factory\Schemas\Fields\StringField;
use Lkt\Factory\Schemas\Schema;
use Lkt\Generated\GeneratedLktImportType;
use Lkt\Locale\Locale;

class LktImportType extends GeneratedLktImportType
{
    const COMPONENT = 'lkt-import-type';

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