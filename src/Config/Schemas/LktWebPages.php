<?php

namespace Lkt\Config\Schemas;

use Lkt\Enums\LaminimComponent;
use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\ForeignKeyField;
use Lkt\Factory\Schemas\Fields\IntegerField;
use Lkt\Factory\Schemas\Fields\JSONField;
use Lkt\Factory\Schemas\Fields\PivotField;
use Lkt\Factory\Schemas\Fields\PivotLeftIdField;
use Lkt\Factory\Schemas\Fields\PivotPositionField;
use Lkt\Factory\Schemas\Fields\PivotRightIdField;
use Lkt\Factory\Schemas\Fields\RelatedField;
use Lkt\Factory\Schemas\Fields\StringField;
use Lkt\Factory\Schemas\InstanceSettings;
use Lkt\Factory\Schemas\Schema;
use Lkt\Instances\LktUser;
use Lkt\Instances\LktWebElementPivotWebElement;
use Lkt\Instances\LktWebPage;
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
        ->setFields([
            IntegerField::identifier('id'),

            DateTimeField::define('createdAt', 'created_at')
                ->setDefaultReadFormat('Y-m-d')
                ->setCurrentTimeStampAsDefaultValue(),

            DateTimeField::define('updatedAt', 'updated_at')
                ->setDefaultReadFormat('Y-m-d')
                ->setCurrentTimeStampAsDefaultValue()
                ->setCurrentTimeStampOnUpdate(),

            DateTimeField::define('publishedAt', 'published_at')
                ->setDefaultReadFormat('Y-m-d')
                ->setCurrentTimeStampAsDefaultValue(),

            ForeignKeyField::defineRelation(LaminimComponent::User->value, 'createdBy', 'created_by')->setDefaultValue([LktUser::class, 'getSignedInUserId']),
            IntegerField::enumChoice(WebPageStatus::class, 'status'),
            IntegerField::define('type'),
            StringField::i18n('name'),
            JSONField::associativeI18n('nameData', 'name'),

            RelatedField::single(LaminimComponent::WebPageMetas->value, 'metas', 'webPage')
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
                ->setCompositionValue('webCategory', 'id'),

            PivotField::definePivot(LaminimComponent::WebElement->value, 'lkt_web_pages__web_elements', 'webElements', 'web_page_id', LaminimComponent::WebPagePivotWebElement->value)
                ->setPivotLeftIdField(PivotLeftIdField::defineRelation(LaminimComponent::WebPage->value, 'user', 'web_page_id'))
                ->setPivotRightIdField(PivotRightIdField::defineRelation(LaminimComponent::WebElement->value, 'role', 'web_element_id'))
                ->setPivotPositionField(PivotPositionField::define('position'))
                ->setPivotInstanceConfig(LktWebElementPivotWebElement::class, 'Lkt\Generated', __DIR__ . '/../../Generated')
                ->setRelatedAccessPolicies([
                    'r-app-menu' => 'r-app-menu'
                ]),
        ])

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