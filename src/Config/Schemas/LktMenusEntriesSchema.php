<?php

namespace Lkt\Config\Schemas;

use Lkt\Enums\LaminimComponent;
use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\IntegerChoiceField;
use Lkt\Factory\Schemas\Fields\IntegerField;
use Lkt\Factory\Schemas\Fields\JSONField;
use Lkt\Factory\Schemas\Fields\MethodGetterField;
use Lkt\Factory\Schemas\Fields\PivotField;
use Lkt\Factory\Schemas\Fields\PivotLeftIdField;
use Lkt\Factory\Schemas\Fields\PivotPositionField;
use Lkt\Factory\Schemas\Fields\PivotRightIdField;
use Lkt\Factory\Schemas\Fields\StringField;
use Lkt\Factory\Schemas\InstanceSettings;
use Lkt\Factory\Schemas\Schema;
use Lkt\Http\Enums\AccessLevel;
use Lkt\Instances\LktMenuEntry;
use Lkt\Instances\LktMenuEntryPivotEntry;
use Lkt\Instances\LktMenuPivotEntry;
use Lkt\Menus\Enums\MenuEntryType;

Schema::add(
    Schema::table('lkt_menus_entries', LaminimComponent::MenuEntry->value)
        ->setInstanceSettings(
            InstanceSettings::define(LktMenuEntry::class)
                ->setNamespaceForGeneratedClass('Lkt\Generated')
                ->setWhereStoreGeneratedClass(__DIR__ . '/../../Generated')
        )
        ->setItemsPerPage(20)
        ->setCountableField('id')
        ->setRelatedAccessPolicy([
            'id' => 'value',
            'name' => 'label',
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

            StringField::i18n('name'),
            JSONField::associativeI18n('nameData', 'name'),
            IntegerChoiceField::enumChoice(MenuEntryType::class, 'type'),
            IntegerChoiceField::enumChoice(AccessLevel::class, 'accessLevel', 'access_level'),
            StringField::define('component')->setDefaultValue(''),
            StringField::define('url')->setDefaultValue(''),
            StringField::define('route')->setDefaultValue(''),
            IntegerField::define('itemId', 'item_id'),
            MethodGetterField::define('getReadMenuTo', 'to'),

            PivotField::definePivot(LaminimComponent::Menu->value, 'lkt_menus__entries', 'menus', 'entry_id', LaminimComponent::MenuPivotEntry->value)
                ->setPivotLeftIdField(PivotLeftIdField::defineRelation(LaminimComponent::Menu->value, 'menu', 'menu_id'))
                ->setPivotRightIdField(PivotRightIdField::defineRelation(LaminimComponent::MenuEntry->value, 'entry', 'entry_id'))
                ->setPivotPositionField(PivotPositionField::define('position'))
                ->setPivotInstanceConfig(LktMenuPivotEntry::class, 'Lkt\Generated', __DIR__ . '/../../Generated'),

            PivotField::definePivot(LaminimComponent::MenuEntry->value, 'lkt_menus_entries__children', 'children', 'parent_id', LaminimComponent::MenuEntryPivotMenuEntry->value)
                ->setPivotRightIdField(PivotRightIdField::defineRelation(LaminimComponent::MenuEntry->value, 'child', 'child_id'))
                ->setPivotLeftIdField(PivotLeftIdField::defineRelation(LaminimComponent::MenuEntry->value, 'parent', 'parent_id'))
                ->setPivotPositionField(PivotPositionField::define('position'))
                ->setPivotInstanceConfig(LktMenuEntryPivotEntry::class, 'Lkt\Generated', __DIR__ . '/../../Generated')
                ->setRelatedAccessPolicies([
                    'r-app-menu' => 'r-app-menu'
                ]),
        ])
        ->addAccessPolicy('write', ['nameData', 'includeAvailableAdminRoutes', 'type', 'url', 'component', 'itemId', 'accessLevel'])
        ->addAccessPolicy('r-app-menu', [
            'to',
            'name' => 'text',
            'children',
        ])
        ->addAccessPolicy('admin', [
            'id',
            'nameData',
            'includeAvailableAdminRoutes',
            'url',
            'route',
            'type',
            'component',
            'accessLevel',
            'itemId',
            'children',
        ])
);