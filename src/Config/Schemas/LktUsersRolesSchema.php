<?php

namespace Lkt\Config\Schemas;

use Lkt\Enums\LaminimComponent;
use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\IntegerField;
use Lkt\Factory\Schemas\Fields\JSONField;
use Lkt\Factory\Schemas\Fields\StringField;
use Lkt\Factory\Schemas\InstanceSettings;
use Lkt\Factory\Schemas\Schema;
use Lkt\Instances\LktUserRole;

Schema::add(
    Schema::table('lkt_users_roles', LaminimComponent::UserRole->value)
        ->setInstanceSettings(
            InstanceSettings::define(LktUserRole::class)
                ->setNamespaceForGeneratedClass('Lkt\Generated')
                ->setWhereStoreGeneratedClass(__DIR__ . '/../../Generated')
        )
        ->setRelatedAccessPolicy([
            'id' => 'value',
            'name' => 'label',
            'id',
            'name',
            'nameData',
        ])
        ->setItemsPerPage(20)
        ->setCountableField('id')
        ->setIncludeDuplicatedTextInField('nameData')
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
            JSONField::associative('permissions'),
        ])
        ->addAccessPolicy('admin', ['id', 'nameData', 'permissions'])
        ->addAccessPolicy('duplicate', ['nameData', 'permissions'])
);