<?php

namespace Lkt\Config\Schemas;

use Lkt\Factory\Schemas\Fields\AssocJSONField;
use Lkt\Factory\Schemas\Fields\BooleanField;
use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\IdField;
use Lkt\Factory\Schemas\Fields\StringField;
use Lkt\Factory\Schemas\InstanceSettings;
use Lkt\Factory\Schemas\Schema;
use Lkt\Instances\LktFileFormat;


Schema::add(
    Schema::table('lkt_file_formats', LktFileFormat::COMPONENT)
        ->setInstanceSettings(
            InstanceSettings::define(LktFileFormat::class)
                ->setNamespaceForGeneratedClass('Lkt\Generated')
                ->setWhereStoreGeneratedClass(__DIR__ . '/../../Generated')
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
                ->setCurrentTimeStampOnUpdate()
        )
        ->addField(StringField::define('name')->setIsI18nJson()->setIsUnique())
        ->addField(AssocJSONField::define('nameData', 'name')->setIsI18nJson())
        ->addField(StringField::define('description')->setIsI18nJson())
        ->addField(AssocJSONField::define('descriptionData', 'description')->setIsI18nJson())
        ->addField(BooleanField::define('isActive', 'is_active'))
);