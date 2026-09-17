<?php

namespace Lkt\Factory\Schemas\PrefabFields;

use Lkt\Enums\LaminimComponent;
use Lkt\Factory\Schemas\Enums\PrefabRole;
use Lkt\Factory\Schemas\Fields\IntegerField;

class RelatedFileEntityField
{
    final public static function define(string $name = 'fileEntity', string $column = 'file_entity_id'): IntegerField
    {
        return IntegerField::foreignKey(LaminimComponent::FileEntity->value, $name, $column)->setPrefabRole(PrefabRole::RelatedFileEntity);
    }
}