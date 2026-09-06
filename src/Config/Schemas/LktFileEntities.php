<?php

namespace Lkt\Config\Schemas;

use Lkt\Enums\LaminimComponent;
use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\FileField;
use Lkt\Factory\Schemas\Fields\ForeignKeyField;
use Lkt\Factory\Schemas\Fields\IntegerField;
use Lkt\Factory\Schemas\Fields\JSONField;
use Lkt\Factory\Schemas\Fields\RelatedField;
use Lkt\Factory\Schemas\Fields\StringField;
use Lkt\Factory\Schemas\InstanceSettings;
use Lkt\Factory\Schemas\Schema;
use Lkt\FileBrowser\Enums\FileEntityType;
use Lkt\Instances\LktFileEntity;
use Lkt\Instances\LktUser;

Schema::add(
    Schema::table('lkt_file_entities', LaminimComponent::FileEntity->value)
        ->setInstanceSettings(
            InstanceSettings::define(LktFileEntity::class)
                ->setNamespaceForGeneratedClass('Lkt\Generated')
                ->setWhereStoreGeneratedClass(__DIR__ . '/../../Generated')
        )
        ->setItemsPerPage(20)
        ->setCountableField('id')
        ->setRelatedAccessPolicy([
            'id' => 'value',
            'component' => 'label',
            'id',
            'type',
            'config',
            'src',
            'embedCode',
            'langSpecificEmbedCodeData',
            'name',
            'nameData',
//            'children',
        ])
        ->setFields([
            IntegerField::identifier('id'),

            DateTimeField::define('createdAt', 'created_at')
                ->setDefaultReadFormat('Y-m-d')
                ->setCurrentTimeStampAsDefaultValue(),

            DateTimeField::define('updatedAt', 'updated_at')
                ->setDefaultReadFormat('Y-m-d')
                ->setCurrentTimeStampAsDefaultValue()
                ->setCurrentTimeStampOnUpdate(),

            IntegerField::enumChoice(FileEntityType::class, 'type'),

            FileField::define('src')
                ->setStorePath([LktFileEntity::class, 'getSchemaStorePath'])
                ->setPublicPath([LktFileEntity::class, 'getSchemaPublicPath']),

            ForeignKeyField::defineRelation(LaminimComponent::User->value, 'createdBy', 'created_by')->setDefaultValue([LktUser::class, 'getSignedInUserId']),
            StringField::define('embedCode', 'embed_code'),

            JSONField::associative('config'),
            ForeignKeyField::defineRelation(LaminimComponent::FileEntity->value, 'parent', 'parent_id'),
            RelatedField::defineRelation(LaminimComponent::FileEntity->value, 'children', 'parent_id'),
            StringField::i18n('name'),
            JSONField::associativeI18n('nameData', 'name'),
            StringField::i18n('langSpecificEmbedCode', 'lang_specific_embed_code'),
            JSONField::associativeI18n('langSpecificEmbedCodeData', 'lang_specific_embed_code'),
        ])
        ->addAccessPolicy('admin', [
            'id',
            'type',
            'config',
            'src',
            'embedCode',
            'name',
            'nameData',
            'langSpecificEmbedCodeData',
            'children',
            'createdBy',
        ])
);