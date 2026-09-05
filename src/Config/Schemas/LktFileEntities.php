<?php

namespace Lkt\Config\Schemas;

use Lkt\Enums\LaminimComponent;
use Lkt\Factory\Schemas\Fields\AssocJSONField;
use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\FileField;
use Lkt\Factory\Schemas\Fields\ForeignKeyField;
use Lkt\Factory\Schemas\Fields\IntegerChoiceField;
use Lkt\Factory\Schemas\Fields\IntegerField;
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
        ->addField(IntegerField::identifier('id'))
        ->addField(
            DateTimeField::define('createdAt', 'created_at')
                ->setDefaultReadFormat('Y-m-d')
                ->setCurrentTimeStampAsDefaultValue()
        )
        ->addField(
            DateTimeField::define('updatedAt', 'updated_at')
                ->setDefaultReadFormat('Y-m-d')
                ->setCurrentTimeStampAsDefaultValue()
                ->setCurrentTimeStampOnUpdate()
        )
        ->addField(IntegerChoiceField::enumChoice(FileEntityType::class, 'type'))
        ->addField(
            FileField::define('src')
                ->setStorePath([LktFileEntity::class, 'getSchemaStorePath'])
                ->setPublicPath([LktFileEntity::class, 'getSchemaPublicPath'])
        )
        ->addField(ForeignKeyField::defineRelation(LaminimComponent::User->value, 'createdBy', 'created_by')->setDefaultValue([LktUser::class, 'getSignedInUserId']))
        ->addField(StringField::define('embedCode', 'embed_code'))
        ->addField(AssocJSONField::define('config'))
        ->addField(
            ForeignKeyField::defineRelation(LaminimComponent::FileEntity->value, 'parent', 'parent_id')
        )
        ->addField(
            RelatedField::defineRelation(LaminimComponent::FileEntity->value, 'children', 'parent_id')
        )
        ->addField(StringField::define('name')->setIsI18nJson())
        ->addField(AssocJSONField::define('nameData', 'name')->setIsI18nJson())
        ->addField(StringField::define('langSpecificEmbedCode', 'lang_specific_embed_code')->setIsI18nJson())
        ->addField(AssocJSONField::define('langSpecificEmbedCodeData', 'lang_specific_embed_code')->setIsI18nJson())

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