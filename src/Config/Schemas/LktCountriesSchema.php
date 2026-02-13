<?php

namespace Lkt\Config\Schemas;

use Lkt\Factory\Schemas\Fields\AssocJSONField;
use Lkt\Factory\Schemas\Fields\BooleanField;
use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\IdField;
use Lkt\Factory\Schemas\Fields\StringField;
use Lkt\Factory\Schemas\InstanceSettings;
use Lkt\Factory\Schemas\Schema;
use Lkt\Instances\LktCountry;

Schema::add(
    Schema::table('lkt_countries', LktCountry::COMPONENT)
        ->setInstanceSettings(
            InstanceSettings::define(LktCountry::class)
                ->setNamespaceForGeneratedClass('Lkt\Generated')
                ->setWhereStoreGeneratedClass(__DIR__ . '/../../Generated')
        )
        ->setItemsPerPage(20)
        ->setCountableField('id')
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

        ->addField(StringField::define('isoCodeCCA2', 'iso_code_cca2'))
        ->addField(StringField::define('isoCodeCCN3', 'iso_code_ccn3'))

        ->addField(BooleanField::define('syncExcluded', 'sync_excluded'))

        ->addField(StringField::define('name')->setIsI18nJson())
        ->addField(AssocJSONField::define('nameData', 'name')->setIsI18nJson())
);