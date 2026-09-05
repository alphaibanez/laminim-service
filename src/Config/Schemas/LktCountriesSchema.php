<?php

namespace Lkt\Config\Schemas;

use Lkt\Enums\LaminimComponent;
use Lkt\Factory\Schemas\Fields\AssocJSONField;
use Lkt\Factory\Schemas\Fields\BooleanField;
use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\IntegerField;
use Lkt\Factory\Schemas\Fields\JSONField;
use Lkt\Factory\Schemas\Fields\RelatedField;
use Lkt\Factory\Schemas\Fields\StringField;
use Lkt\Factory\Schemas\InstanceSettings;
use Lkt\Factory\Schemas\Schema;
use Lkt\Instances\LktCountry;

Schema::add(
    Schema::table('lkt_countries', LaminimComponent::Country->value)
        ->setInstanceSettings(InstanceSettings::simple(LktCountry::class, 'Lkt\Generated', __DIR__ . '/../../Generated')
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

            StringField::define('isoCodeAlpha2', 'iso_code_alpha2'),
            StringField::define('isoCodeNumeric3', 'iso_code_numeric3'),

            BooleanField::define('syncExcluded', 'sync_excluded'),
            StringField::i18n('name'),

            JSONField::associativeI18n('nameData', 'name'),
            RelatedField::defineRelation(LaminimComponent::CountryState->value, 'states', 'country_id'),
        ])

        ->addAccessPolicy('admin', [
            'id',
            'nameData',
            'isoCodeAlpha2',
            'isoCodeNumeric3',
            'syncExcluded',
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