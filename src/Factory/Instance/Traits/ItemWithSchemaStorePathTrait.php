<?php

namespace Lkt\Factory\Instance\Traits;

use Lkt\FileBrowser\Enums\FileEntityType;
use Lkt\Instances\LktFileEntity;

trait ItemWithSchemaStorePathTrait
{
    public static $schemaStorePath = null;
    public static $schemaPublicPath = null;


    public static function getSchemaStorePath($instance): string
    {
        if (is_callable(static::$schemaStorePath)) {
            return call_user_func(static::$schemaStorePath, $instance);
        }
        return '';
    }


    public static function getSchemaPublicPath(LktFileEntity|null $instance = null): string
    {
        if ($instance instanceof LktFileEntity) {
            if ($instance->getType() === FileEntityType::StorageUnit->value || $instance->getType() === FileEntityType::Directory->value) return '';
        }

        if (is_callable(static::$schemaPublicPath)) {
            return call_user_func(static::$schemaPublicPath, $instance);
        }
        return '';
    }
}