<?php

namespace Lkt\Config\Schemas;

use Lkt\Factory\Schemas\Fields\AssocJSONField;
use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\ForeignKeyField;
use Lkt\Factory\Schemas\Fields\IdField;
use Lkt\Factory\Schemas\Fields\StringField;
use Lkt\Factory\Schemas\InstanceSettings;
use Lkt\Factory\Schemas\Schema;
use Lkt\Instances\LktUser;
use Lkt\Instances\LktWebPage;
use Lkt\Instances\LktWebPageSlug;

Schema::add(
    Schema::table('lkt_web_pages__slugs', LktWebPageSlug::COMPONENT)
        ->setInstanceSettings(
            InstanceSettings::define(LktWebPageSlug::class)
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
        ->addField(StringField::define('slug')->setIsI18nJson()->setIsUnique())
        ->addField(AssocJSONField::define('slugData', 'name')->setIsI18nJson())
        ->addField(ForeignKeyField::defineRelation(LktWebPage::COMPONENT, 'webPage', 'page_id')->setDefaultValue(0))
        ->addField(ForeignKeyField::defineRelation(LktWebPage::COMPONENT, 'webCategory', 'category_id')->setDefaultValue(0))

        ->addAccessPolicy('admin', [
            'id',
            'name',
            'config',
        ])
);