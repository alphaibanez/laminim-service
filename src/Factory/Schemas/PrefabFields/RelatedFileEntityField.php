<?php

namespace Lkt\Factory\Schemas\PrefabFields;

use Lkt\Factory\Schemas\Enums\PrefabRole;
use Lkt\Factory\Schemas\Fields\ForeignKeyField;
use Lkt\Instances\LktFileEntity;

class RelatedFileEntityField
{
    final public static function define(string $name = 'fileEntity', string $column = 'file_entity_id'): ForeignKeyField
    {
        return ForeignKeyField::defineRelation(LktFileEntity::COMPONENT, $name, $column)->setPrefabRole(PrefabRole::RelatedFileEntity);
    }
}