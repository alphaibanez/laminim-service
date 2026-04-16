<?php

namespace Lkt\Config\Schemas;

use Lkt\Factory\Schemas\Fields\AssocJSONField;
use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\ForeignKeyField;
use Lkt\Factory\Schemas\Fields\IdField;
use Lkt\Factory\Schemas\Fields\RelatedField;
use Lkt\Factory\Schemas\Fields\StringField;
use Lkt\Factory\Schemas\InstanceSettings;
use Lkt\Factory\Schemas\Schema;
use Lkt\Instances\LktUser;
use Lkt\Instances\LktWebPageCategory;
use Lkt\Instances\LktWebPageMetas;

Schema::add(
    Schema::table('lkt_web_pages__categories', LktWebPageCategory::COMPONENT)
        ->setInstanceSettings(
            InstanceSettings::define(LktWebPageCategory::class)
                ->setNamespaceForGeneratedClass('Lkt\Generated')
                ->setWhereStoreGeneratedClass(__DIR__ . '/../../Generated')
        )
        ->setItemsPerPage(20)
        ->setCountableField('id')
        ->addField(IdField::define('id'))
        ->addField(
            DateTimeField::define('createdAt', 'created_at')
                ->setDefaultReadFormat('Y-m-d')
                ->setCurrentTimeStampAsDefaultValue()
        )
        ->addField(
            DateTimeField::define('updatedAt', 'updated_at')
                ->setDefaultReadFormat('Y-m-d')
                ->setCurrentTimeStampAsDefaultValue()
        )
        ->addField(ForeignKeyField::defineRelation(LktUser::COMPONENT, 'createdBy', 'created_by')->setDefaultValue([LktUser::class, 'getSignedInUserId']))
        ->addField(StringField::define('name')->setIsI18nJson()->setIsUnique())
        ->addField(AssocJSONField::define('nameData', 'name')->setIsI18nJson())
        ->addField(AssocJSONField::define('config'))

        ->addField(
            RelatedField::defineRelation(LktWebPageMetas::COMPONENT, 'metas', 'webCategory')
                ->setSingleMode()
                ->setCompositionContent([
                    'slug' => 'slug',
                    'slugData' => 'slugData',
                    'description' => 'description',
                    'descriptionData' => 'descriptionData',
                    'keywords' => 'keywords',
                    'keywordsData' => 'keywordsData',
                    'preventRobotsIndex' => 'preventRobotsIndex',
                    'preventRobotsFollow' => 'preventRobotsFollow',
                ])
                ->setCompositionValue('webCategory', 'id')
        )

        ->addAccessPolicy('admin', [
            'id',
            'nameData',
            'config',
            'keywordsData',
            'slugData',
            'descriptionData',
            'preventRobotsIndex',
            'preventRobotsFollow',
        ], [
            'keywordsData',
            'slugData',
            'descriptionData',
            'preventRobotsIndex',
            'preventRobotsFollow',
            ])
);