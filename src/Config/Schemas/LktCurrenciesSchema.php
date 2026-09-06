<?php

namespace Lkt\Config\Schemas;

use Lkt\Enums\LaminimComponent;
use Lkt\Factory\Schemas\Fields\BooleanField;
use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\FloatField;
use Lkt\Factory\Schemas\Fields\IntegerField;
use Lkt\Factory\Schemas\Fields\JSONField;
use Lkt\Factory\Schemas\Fields\StringField;
use Lkt\Factory\Schemas\InstanceSettings;
use Lkt\Factory\Schemas\Schema;
use Lkt\Instances\LktCurrency;

Schema::add(
    Schema::table('lkt_currencies', LaminimComponent::Currency->value)
        ->setInstanceSettings(InstanceSettings::simple(LktCurrency::class, 'Lkt\Generated', __DIR__ . '/../../Generated')
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

            StringField::define('isoCodeAlpha3', 'iso_code_alpha3'),
            StringField::define('isoCodeNumeric3', 'iso_code_numeric3'),

            BooleanField::define('syncExcluded', 'sync_excluded'),
            FloatField::define('factorToDefault', 'factor_to_Default'),

            StringField::i18n('name'),
            JSONField::associativeI18n('nameData', 'name'),
        ])

        ->addAccessPolicy('admin', [
            'id',
            'nameData',
            'isoCodeAlpha3',
            'isoCodeNumeric3',
            'syncExcluded',
            'factorToDefault',
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