<?php

namespace Lkt\Config\Schemas;

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
use Lkt\Instances\LktMenu;
use Lkt\Instances\LktMenuEntry;
use Lkt\Instances\LktMenuPivotEntry;
use Lkt\Menus\Enums\MenuEntryType;

Schema::add(
    Schema::table('lkt_menus_entries', LktMenuEntry::COMPONENT)
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
        )
        ->addField(StringField::define('name')->setIsI18nJson())
        ->addField(AssocJSONField::define('nameData', 'name')->setIsI18nJson())
        ->addField(IntegerChoiceField::enumChoice(MenuEntryType::class, 'type'))
        ->addField(IntegerChoiceField::enumChoice(AccessLevel::class, 'accessLevel', 'access_level'))
        ->addField(StringField::define('component'))
        ->addField(StringField::define('url'))
        ->addField(IntegerField::define('itemId', 'item_id'))
        ->addField(MethodGetterField::define('getReadMenuTo', 'to'))
        ->addField(PivotField::definePivot(LktMenu::COMPONENT, 'lkt_menus__entries', 'menus', 'entry_id')
            ->setPivotLeftIdField(PivotLeftIdField::defineRelation(LktMenu::COMPONENT, 'menu', 'menu_id'))
            ->setPivotRightIdField(PivotRightIdField::defineRelation(LktMenuEntry::COMPONENT, 'entry', 'entry_id'))
            ->setPivotPositionField(PivotPositionField::define('position'))
            ->setPivotInstanceConfig(LktMenuPivotEntry::class, 'Lkt\Generated', __DIR__ . '/../../Generated')
        )
        ->addAccessPolicy('write', ['nameData', 'includeAvailableAdminRoutes', 'type', 'url', 'component', 'itemId', 'accessLevel'])
        ->addAccessPolicy('r-app-menu', [
            'to',
            'name' => 'text',
        ])
);