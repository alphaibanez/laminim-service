<?php

namespace Lkt\Config\Schemas;

use Lkt\Enums\LaminimComponent;
use Lkt\Factory\Schemas\Fields\AssocJSONField;
use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\IdField;
use Lkt\Factory\Schemas\Fields\IntegerChoiceField;
use Lkt\Factory\Schemas\Fields\IntegerField;
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
        ->addField(StringField::define('name')->setIsI18nJson())
        ->addField(AssocJSONField::define('nameData', 'name')->setIsI18nJson())
        ->addField(IntegerChoiceField::enumChoice(MenuEntryType::class, 'type'))
        ->addField(IntegerChoiceField::enumChoice(AccessLevel::class, 'accessLevel', 'access_level'))
        ->addField(StringField::define('component')->setDefaultValue(''))
        ->addField(StringField::define('url')->setDefaultValue(''))
        ->addField(StringField::define('route')->setDefaultValue(''))
        ->addField(IntegerField::define('itemId', 'item_id'))
        ->addField(MethodGetterField::define('getReadMenuTo', 'to'))
        ->addField(PivotField::definePivot(LaminimComponent::Menu->value, 'lkt_menus__entries', 'menus', 'entry_id', LaminimComponent::MenuPivotEntry->value)
            ->setPivotLeftIdField(PivotLeftIdField::defineRelation(LaminimComponent::Menu->value, 'menu', 'menu_id'))
            ->setPivotRightIdField(PivotRightIdField::defineRelation(LaminimComponent::MenuEntry->value, 'entry', 'entry_id'))
            ->setPivotPositionField(PivotPositionField::define('position'))
            ->setPivotInstanceConfig(LktMenuPivotEntry::class, 'Lkt\Generated', __DIR__ . '/../../Generated')
        )
        ->addField(PivotField::definePivot(LaminimComponent::MenuEntry->value, 'lkt_menus_entries__children', 'children', 'parent_id', LaminimComponent::MenuEntryPivotMenuEntry->value)
            ->setPivotRightIdField(PivotRightIdField::defineRelation(LaminimComponent::MenuEntry->value, 'child', 'child_id'))
            ->setPivotLeftIdField(PivotLeftIdField::defineRelation(LaminimComponent::MenuEntry->value, 'parent', 'parent_id'))
            ->setPivotPositionField(PivotPositionField::define('position'))
            ->setPivotInstanceConfig(LktMenuEntryPivotEntry::class, 'Lkt\Generated', __DIR__ . '/../../Generated')
            ->setRelatedAccessPolicies([
                'r-app-menu' => 'r-app-menu'
            ])
        )
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