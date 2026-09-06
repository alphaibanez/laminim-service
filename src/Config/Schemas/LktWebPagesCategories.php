<?php

namespace Lkt\Config\Schemas;

use Lkt\Enums\LaminimComponent;
use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\ForeignKeyField;
use Lkt\Factory\Schemas\Fields\IntegerField;
use Lkt\Factory\Schemas\Fields\JSONField;
use Lkt\Factory\Schemas\Fields\RelatedField;
use Lkt\Factory\Schemas\Fields\StringField;
use Lkt\Factory\Schemas\InstanceSettings;
use Lkt\Factory\Schemas\Schema;
use Lkt\Instances\LktUser;
use Lkt\Instances\LktWebPageCategory;

Schema::add(
    Schema::table('lkt_web_pages__categories', LaminimComponent::WebPageCategory->value)
        ->setInstanceSettings(
            InstanceSettings::define(LktWebPageCategory::class)
                ->setNamespaceForGeneratedClass('Lkt\Generated')
                ->setWhereStoreGeneratedClass(__DIR__ . '/../../Generated')
        )
        ->setItemsPerPage(20)
        ->setCountableField('id')
        ->setFields([
            IntegerField::identifier('id'),

            DateTimeField::define('createdAt', 'created_at')
                ->setDefaultReadFormat('Y-m-d')
                ->setCurrentTimeStampAsDefaultValue(),


            DateTimeField::define('updatedAt', 'updated_at')
                ->setDefaultReadFormat('Y-m-d')
                ->setCurrentTimeStampAsDefaultValue()
                ->setCurrentTimeStampOnUpdate(),

            ForeignKeyField::defineRelation(LaminimComponent::User->value, 'createdBy', 'created_by')->setDefaultValue([LktUser::class, 'getSignedInUserId']),

            StringField::i18n('name')->setIsUnique(),
            JSONField::associativeI18n('nameData', 'name'),
            JSONField::associative('config'),

            RelatedField::single(LaminimComponent::WebPageMetas->value, 'metas', 'webCategory')
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
                ->setCompositionValue('webCategory', 'id'),

        ])

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