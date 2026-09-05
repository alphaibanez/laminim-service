<?php

namespace Lkt\Config\Schemas;

use Lkt\Enums\LaminimComponent;
use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\ForeignKeyField;
use Lkt\Factory\Schemas\Fields\IntegerField;
use Lkt\Factory\Schemas\Fields\JSONField;
use Lkt\Factory\Schemas\Fields\StringField;
use Lkt\Factory\Schemas\InstanceSettings;
use Lkt\Factory\Schemas\Schema;
use Lkt\Instances\LktCountryState;

Schema::add(
    Schema::table('lkt_countries_states', LaminimComponent::CountryState->value)
        ->setInstanceSettings(InstanceSettings::simple(LktCountryState::class, 'Lkt\Generated', __DIR__ . '/../../Generated')
            ->setAbstractInstanceExtends(false)
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

            ForeignKeyField::defineRelation(LaminimComponent::Country->value, 'country', 'country_id'),

            StringField::i18n('name'),
            JSONField::associativeI18n('nameData', 'name'),
        ])

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