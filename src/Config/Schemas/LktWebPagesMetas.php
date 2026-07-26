<?php

namespace Lkt\Config\Schemas;

use Lkt\Enums\LaminimComponent;
use Lkt\Factory\Schemas\Fields\AssocJSONField;
use Lkt\Factory\Schemas\Fields\BooleanField;
use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\ForeignKeyField;
use Lkt\Factory\Schemas\Fields\IdField;
use Lkt\Factory\Schemas\Fields\StringField;
use Lkt\Factory\Schemas\InstanceSettings;
use Lkt\Factory\Schemas\Schema;
use Lkt\Instances\LktUser;
use Lkt\Instances\LktWebPage;
use Lkt\Instances\LktWebPageMetas;

Schema::add(
    Schema::table('lkt_web_pages__metas', LaminimComponent::WebPageMetas->value)
        ->setInstanceSettings(
            InstanceSettings::define(LktWebPageMetas::class)
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
                ->setCurrentTimeStampOnUpdate()
        )
        ->addField(ForeignKeyField::defineRelation(LktUser::COMPONENT, 'createdBy', 'created_by')->setDefaultValue([LktUser::class, 'getSignedInUserId']))
        ->addField(StringField::define('slug')->setIsI18nJson())
        ->addField(AssocJSONField::define('slugData', 'slug')->setIsI18nJson())
        ->addField(StringField::define('keywords')->setIsI18nJson())
        ->addField(AssocJSONField::define('keywordsData', 'keywords')->setIsI18nJson())
        ->addField(StringField::define('description')->setIsI18nJson())
        ->addField(AssocJSONField::define('descriptionData', 'description')->setIsI18nJson())
        ->addField(ForeignKeyField::defineRelation(LktWebPage::COMPONENT, 'webPage', 'page_id')->setDefaultValue(0))
        ->addField(ForeignKeyField::defineRelation(LktWebPage::COMPONENT, 'webCategory', 'category_id')->setDefaultValue(0))
        ->addField(BooleanField::define('preventRobotsIndex', 'prevent_robots_index'))
        ->addField(BooleanField::define('preventRobotsFollow', 'prevent_robots_follow'))

        ->addAccessPolicy('admin', [
            'id',
            'slugData',
            'keywordsData',
            'preventRobotsIndex',
            'preventRobotsFollow',
            'webPage',
            'webCategory',
        ])
);