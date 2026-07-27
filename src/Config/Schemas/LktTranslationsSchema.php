<?php

namespace Lkt\Config\Schemas;

use Lkt\Enums\LaminimComponent;
use Lkt\Factory\Schemas\Fields\AssocJSONField;
use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\ForeignKeyField;
use Lkt\Factory\Schemas\Fields\IdField;
use Lkt\Factory\Schemas\Fields\RelatedField;
use Lkt\Factory\Schemas\Fields\StringChoiceField;
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
        ->addField(StringChoiceField::choice(TranslationType::getChoiceOptions(), 'type'))
        ->addField(StringField::define('property'))
        ->addField(StringField::define('value')->setIsI18nJson())
        ->addField(AssocJSONField::define('valueData', 'value')->setIsI18nJson())
        ->addField(ForeignKeyField::defineRelation(LaminimComponent::Translation->value, 'parent', 'parent_id'))
        ->addField(RelatedField::defineRelation(LaminimComponent::Translation->value, 'children', 'parent_id')->setOrder(LktTranslationOrderBy::propertyASC()))
        ->addAccessPolicy('write', ['type', 'property', 'valueData', 'parent', 'children'])
);