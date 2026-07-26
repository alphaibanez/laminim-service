<?php

namespace Lkt\Config\Schemas;

use Lkt\Enums\LaminimComponent;
use Lkt\Factory\Schemas\Fields\AssocJSONField;
use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\ForeignKeyField;
use Lkt\Factory\Schemas\Fields\ForeignKeysField;
use Lkt\Factory\Schemas\Fields\IdField;
use Lkt\Factory\Schemas\Fields\IntegerChoiceField;
use Lkt\Factory\Schemas\Fields\IntegerField;
use Lkt\Factory\Schemas\Fields\RelatedField;
use Lkt\Factory\Schemas\Fields\StringField;
use Lkt\Factory\Schemas\InstanceSettings;
use Lkt\Factory\Schemas\Schema;
use Lkt\Instances\LktUser;
use Lkt\Instances\LktWebElement;
use Lkt\Instances\LktWebPage;
use Lkt\Instances\LktWebPageMetas;
use Lkt\WebPages\Enums\WebPageStatus;


Schema::add(
    Schema::table('lkt_web_pages', LaminimComponent::WebPage->value)
        ->setInstanceSettings(
            InstanceSettings::define(LktWebPage::class)
                ->setNamespaceForGeneratedClass('Lkt\Generated')
                ->setWhereStoreGeneratedClass(__DIR__ . '/../../Generated')
        )
        ->setItemsPerPage(20)
        ->setCountableField('id')
        ->setRelatedAccessPolicy([
            'id' => 'value',
            'component' => 'label',
            'id',
            'component',
            'type',
            'props',
            'config',
            'layout',
            'children',
        ])
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
        ->addField(
            DateTimeField::define('publishedAt', 'published_at')
                ->setDefaultReadFormat('Y-m-d')
                ->setCurrentTimeStampAsDefaultValue()
        )
        ->addField(ForeignKeyField::defineRelation(LktUser::COMPONENT, 'createdBy', 'created_by')->setDefaultValue([LktUser::class, 'getSignedInUserId']))
        ->addField(IntegerChoiceField::enumChoice(WebPageStatus::class, 'status'))
        ->addField(IntegerField::define('type'))

        ->addField(StringField::define('name')->setIsI18nJson())
        ->addField(AssocJSONField::define('nameData', 'name')->setIsI18nJson())

        ->addField(
            RelatedField::defineRelation(LktWebPageMetas::COMPONENT, 'metas', 'webPage')
                ->setSingleMode()
                ->setCompositionContent([
                    'slug' => 'slug',
                    'slugData' => 'slugData',
                    'keywords' => 'keywords',
                    'keywordsData' => 'keywordsData',
                    'description' => 'description',
                    'descriptionData' => 'descriptionData',
                    'preventRobotsIndex' => 'preventRobotsIndex',
                    'preventRobotsFollow' => 'preventRobotsFollow',
                ])
                ->setCompositionValue('webCategory', 'id')
        )

        ->addField(
            ForeignKeysField::defineRelation(LktWebElement::COMPONENT, 'webElements', 'web_elements')
        )
        ->addAccessPolicy('public-read', [
            'name',
            'webElements',
            'description',
            'keywords',
            'slug',
            'preventRobotsIndex',
            'preventRobotsFollow',
        ])
);