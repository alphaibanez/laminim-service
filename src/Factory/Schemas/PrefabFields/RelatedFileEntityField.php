<?php

namespace Lkt\Factory\Schemas\PrefabFields;

use Lkt\Enums\LaminimComponent;
use Lkt\Factory\Schemas\Enums\PrefabRole;
use Lkt\Factory\Schemas\Fields\ForeignKeyField;

class RelatedFileEntityField
{
    final public static function define(string $name = 'fileEntity', string $column = 'file_entity_id'): ForeignKeyField
    {
        return ForeignKeyField::defineRelation(LaminimComponent::FileEntity->value, $name, $column)->setPrefabRole(PrefabRole::RelatedFileEntity);
    }
}