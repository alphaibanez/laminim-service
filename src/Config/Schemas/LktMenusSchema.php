<?php

namespace Lkt\Config\Schemas;

use Lkt\Enums\LaminimComponent;
use Lkt\Factory\Schemas\Fields\BooleanField;
use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\ForeignKeyField;
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
use Lkt\Instances\LktMenu;
use Lkt\Instances\LktMenuPivotEntry;
use Lkt\Instances\LktUser;

Schema::add(
    Schema::table('lkt_menus', LaminimComponent::Menu->value)
        ->setInstanceSettings(
            InstanceSettings::define(LktMenu::class)
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

            StringField::i18n('name'),
            JSONField::associativeI18n('nameData', 'name'),
            BooleanField::define('includeAvailableAdminRoutes', 'include_available_admin_routes'),
            MethodGetterField::define('getNavigableEntries', 'navigableEntries'),

            PivotField::definePivot(LaminimComponent::MenuEntry->value, 'lkt_menus__entries', 'entries', 'menu_id', LaminimComponent::MenuPivotEntry->value)
                ->setPivotLeftIdField(PivotLeftIdField::defineRelation(LaminimComponent::Menu->value, 'menu', 'menu_id'))
                ->setPivotRightIdField(PivotRightIdField::defineRelation(LaminimComponent::MenuEntry->value, 'entry', 'entry_id'))
                ->setPivotPositionField(PivotPositionField::define('position'))
                ->setPivotInstanceConfig(LktMenuPivotEntry::class, 'Lkt\Generated', __DIR__ . '/../../Generated')
                ->setRelatedAccessPolicies([
                    'r-app-menu' => 'r-app-menu'
                ])
        ])
        ->addAccessPolicy('write', ['nameData', 'includeAvailableAdminRoutes'])
        ->addAccessPolicy('r-app-menu', ['navigableEntries' => 'entries'])
        ->addAccessPolicy('admin', [
            'id',
            'nameData',
            'includeAvailableAdminRoutes',
            'entries',
        ])
);