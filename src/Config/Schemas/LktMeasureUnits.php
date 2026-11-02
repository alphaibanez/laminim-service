<?php

namespace Lkt\Config\Schemas;

use Lkt\Enums\AccessTokenDuration;
use Lkt\Enums\AccessTokenPurpose;
use Lkt\Enums\LaminimComponent;
use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\IntegerField;
use Lkt\Factory\Schemas\Fields\JSONField;
use Lkt\Factory\Schemas\Fields\StringField;
use Lkt\Factory\Schemas\InstanceSettings;
use Lkt\Factory\Schemas\Schema;
use Lkt\Instances\LktAccessToken;
use Lkt\Instances\LktMeasureUnit;

Schema::add(
    Schema::table('lkt_measure_units', LaminimComponent::MeasureUnit->value)
        ->setInstanceSettings(
            InstanceSettings::define(LktMeasureUnit::class)
                ->setNamespaceForGeneratedClass('Lkt\Generated')
                ->setWhereStoreGeneratedClass(__DIR__ . '/../../Generated')
        )
        ->setItemsPerPage(20)
//        ->setComplexPrimaryKey(['user', 'purpose'])
        ->setFields([
            IntegerField::identifier('id'),

            StringField::i18n('name')->setIsUnique(),
            JSONField::associativeI18n('nameData', 'name'),

            StringField::i18n('abbr')->setIsUnique(),
            JSONField::associativeI18n('abbrData', 'name'),


            DateTimeField::define('createdAt', 'created_at')
                ->setDefaultReadFormat('Y-m-d')
                ->setCurrentTimeStampAsDefaultValue(),

            DateTimeField::define('updatedAt', 'updated_at')
                ->setDefaultReadFormat('Y-m-d')
                ->setCurrentTimeStampAsDefaultValue(),
        ])
);