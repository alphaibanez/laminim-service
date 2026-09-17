<?php

namespace Lkt\Config\Schemas;

use Lkt\Enums\LaminimComponent;
use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\IntegerField;
use Lkt\Factory\Schemas\Fields\JSONField;
use Lkt\Factory\Schemas\Fields\PivotField;
use Lkt\Factory\Schemas\Fields\PivotPositionField;
use Lkt\Factory\Schemas\Fields\StringField;
use Lkt\Factory\Schemas\InstanceSettings;
use Lkt\Factory\Schemas\Schema;
use Lkt\Instances\LktUser;
use Lkt\Instances\LktWebElement;
use Lkt\Instances\LktWebElementPivotWebElement;

Schema::add(
    Schema::table('lkt_web_elements', LaminimComponent::WebElement->value)
        ->setInstanceSettings(InstanceSettings::simple(LktWebElement::class, 'Lkt\Generated', __DIR__ . '/../../Generated'))

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

            IntegerField::foreignKey(LaminimComponent::User->value, 'createdBy', 'created_by')->setDefaultValue([LktUser::class, 'getSignedInUserId']),
            IntegerField::define('type'),
            StringField::define('component'),
            JSONField::associative('props'),
            JSONField::associative('config'),
            JSONField::associative('layout'),
            JSONField::associative('subElements', 'sub_elements'),

            PivotField::definePivot(LaminimComponent::WebElement->value, 'lkt_web_elements__web_elements', 'children', 'parent_id', LaminimComponent::WebElementPivotWebElement->value)
                ->setPivotLeftIdField(IntegerField::leftPivot(LaminimComponent::WebElement->value, 'user', 'parent_id'))
                ->setPivotRightIdField(IntegerField::rightPivot(LaminimComponent::WebElement->value, 'role', 'child_id'))
                ->setPivotPositionField(PivotPositionField::define('position'))
                ->setPivotInstanceConfig(LktWebElementPivotWebElement::class, 'Lkt\Generated', __DIR__ . '/../../Generated')
                ->setRelatedAccessPolicies([
                    'r-app-menu' => 'r-app-menu'
                ]),
        ])

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
            'subElements',
        ])
);