<?php

namespace Lkt\Factory\Schemas\PrefabFields;

use Lkt\Attributes\Deprecated;
use Lkt\Enums\LaminimComponent;
use Lkt\Factory\Schemas\Enums\PrefabRole;
use Lkt\Factory\Schemas\Fields\ForeignKeysField;

/**
 * @deprecated
 */
#[Deprecated]
class RelatedFileEntitiesField
{
    final public static function define(string $name = 'fileEntities', string $column = 'file_entities'): ForeignKeysField
    {
        return ForeignKeysField::defineRelation(LaminimComponent::FileEntity->value, $name, $column)->setPrefabRole(PrefabRole::RelatedFileEntities);
    }
}