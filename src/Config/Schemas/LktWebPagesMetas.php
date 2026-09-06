<?php

namespace Lkt\Config\Schemas;

use Lkt\Enums\LaminimComponent;
use Lkt\Factory\Schemas\Fields\BooleanField;
use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\ForeignKeyField;
use Lkt\Factory\Schemas\Fields\IntegerField;
use Lkt\Factory\Schemas\Fields\JSONField;
use Lkt\Factory\Schemas\Fields\StringField;
use Lkt\Factory\Schemas\InstanceSettings;
use Lkt\Factory\Schemas\Schema;
use Lkt\Instances\LktUser;
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

            StringField::i18n('slug'),
            JSONField::associativeI18n('slugData', 'slug'),
            StringField::i18n('keywords'),
            JSONField::associativeI18n('keywordsData', 'keywords'),
            StringField::i18n('description'),
            JSONField::associativeI18n('descriptionData', 'description'),

            ForeignKeyField::defineRelation(LaminimComponent::WebPage->value, 'webPage', 'page_id')->setDefaultValue(0),
            ForeignKeyField::defineRelation(LaminimComponent::WebPageCategory->value, 'webCategory', 'category_id')->setDefaultValue(0),
            BooleanField::define('preventRobotsIndex', 'prevent_robots_index'),
            BooleanField::define('preventRobotsFollow', 'prevent_robots_follow'),
        ])

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