<?php

namespace Lkt\Config\Schemas;

use Lkt\Enums\LaminimComponent;
use Lkt\Factory\Schemas\Fields\AssocJSONField;
use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\ForeignKeyField;
use Lkt\Factory\Schemas\Fields\IntegerField;
use Lkt\Factory\Schemas\Fields\StringField;
use Lkt\Factory\Schemas\InstanceSettings;
use Lkt\Factory\Schemas\Schema;
use Lkt\Instances\LktCountryState;

Schema::add(
    Schema::table('lkt_countries_states', LaminimComponent::CountryState->value)
        ->setInstanceSettings(
            InstanceSettings::define(LktCountryState::class)
                ->setNamespaceForGeneratedClass('Lkt\Generated')
                ->setWhereStoreGeneratedClass(__DIR__ . '/../../Generated')
        )
        ->setItemsPerPage(20)
        ->setCountableField('id')
        ->addField(IntegerField::identifier('id'))
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

        ->addField(ForeignKeyField::defineRelation(LaminimComponent::Country->value, 'country', 'country_id'))

        ->addField(StringField::define('name')->setIsI18nJson())
        ->addField(AssocJSONField::define('nameData', 'name')->setIsI18nJson())

        ->addAccessPolicy('admin', [
            'id',
            'nameData',
            'country',
        ])

        ->addAccessPolicy('admin-opt', [
            'id' => 'value',
            'name' => 'label',
        ])

        ->setRelatedAccessPolicy([
            'id' => 'value',
            'name' => 'label',
        ])

);