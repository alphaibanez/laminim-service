<?php

namespace Lkt\Factory\Schemas\PrefabFields;

use Lkt\Factory\Schemas\Enums\PrefabRole;
use Lkt\Factory\Schemas\Fields\ForeignKeysField;
use Lkt\Instances\LktFileEntity;

class RelatedFileEntitiesField
{
    final public static function define(string $name = 'fileEntities', string $column = 'file_entities'): ForeignKeysField
    {
        return ForeignKeysField::defineRelation(LktFileEntity::COMPONENT, $name, $column)->setPrefabRole(PrefabRole::RelatedFileEntities);
    }
}