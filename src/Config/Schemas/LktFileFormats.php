<?php

namespace Lkt\Config\Schemas;

use Lkt\Enums\LaminimComponent;
use Lkt\Factory\Schemas\Fields\BooleanField;
use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\IntegerField;
use Lkt\Factory\Schemas\Fields\JSONField;
use Lkt\Factory\Schemas\Fields\StringField;
use Lkt\Factory\Schemas\InstanceSettings;
use Lkt\Factory\Schemas\Schema;
use Lkt\Instances\LktFileFormat;


Schema::add(
    Schema::table('lkt_file_formats', LaminimComponent::FileFormat->value)
        ->setInstanceSettings(
            InstanceSettings::define(LktFileFormat::class)
                ->setNamespaceForGeneratedClass('Lkt\Generated')
                ->setWhereStoreGeneratedClass(__DIR__ . '/../../Generated')
                ->setAbstractInstanceExtends(false)
        )
        ->setItemsPerPage(20)
        ->setCountableField('id')
        ->setRelatedAccessPolicy([
            'id' => 'value',
            'name' => 'label',
            'id',
            'name',
            'description',
        ])
        ->setFields([
            IntegerField::identifier('id'),

            DateTimeField::define('createdAt', 'created_at')
                ->setDefaultReadFormat('Y-m-d')
                ->setCurrentTimeStampAsDefaultValue(),

            DateTimeField::define('updatedAt', 'updated_at')
                ->setDefaultReadFormat('Y-m-d')
                ->setCurrentTimeStampAsDefaultValue()
                ->setCurrentTimeStampOnUpdate(),

            StringField::i18n('name')->setIsUnique(),
            JSONField::associativeI18n('nameData', 'name'),

            StringField::i18n('description')->setIsUnique(),
            JSONField::associativeI18n('descriptionData', 'description'),

            BooleanField::define('isActive', 'is_active'),
        ])
);