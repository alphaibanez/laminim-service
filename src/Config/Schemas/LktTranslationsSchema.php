<?php

namespace Lkt\Config\Schemas;

use Lkt\Enums\LaminimComponent;
use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\ForeignKeyField;
use Lkt\Factory\Schemas\Fields\IntegerField;
use Lkt\Factory\Schemas\Fields\JSONField;
use Lkt\Factory\Schemas\Fields\RelatedField;
use Lkt\Factory\Schemas\Fields\StringField;
use Lkt\Factory\Schemas\InstanceSettings;
use Lkt\Factory\Schemas\Schema;
use Lkt\Generated\LktTranslationOrderBy;
use Lkt\Instances\LktTranslation;
use Lkt\Translations\Enums\TranslationType;

Schema::add(
    Schema::table('lkt_i18n', LaminimComponent::Translation->value)
        ->setInstanceSettings(
            InstanceSettings::define(LktTranslation::class)
                ->setNamespaceForGeneratedClass('Lkt\Generated')
                ->setWhereStoreGeneratedClass(__DIR__ . '/../../Generated')
        )
        ->setItemsPerPage(20)
        ->setCountableField('id')
        ->setRelatedAccessPolicy([
            'id' => 'value',
            'property' => 'label',
            'id',
            'property',
            'type',
            'value',
            'valueData',
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

            StringField::choice(TranslationType::getChoiceOptions(), 'type'),
            StringField::define('property'),

            StringField::i18n('value'),
            JSONField::associativeI18n('valueData', 'value'),
            ForeignKeyField::defineRelation(LaminimComponent::Translation->value, 'parent', 'parent_id'),
            RelatedField::defineRelation(LaminimComponent::Translation->value, 'children', 'parent_id')->setOrder(LktTranslationOrderBy::propertyASC()),
        ])
        ->addAccessPolicy('write', ['type', 'property', 'valueData', 'parent', 'children'])
);