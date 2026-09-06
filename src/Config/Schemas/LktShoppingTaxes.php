<?php

namespace Lkt\Config\Schemas;

use Lkt\Enums\LaminimComponent;
use Lkt\Factory\Schemas\Fields\BooleanField;
use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\FloatField;
use Lkt\Factory\Schemas\Fields\ForeignKeyField;
use Lkt\Factory\Schemas\Fields\IntegerField;
use Lkt\Factory\Schemas\Fields\JSONField;
use Lkt\Factory\Schemas\Fields\StringField;
use Lkt\Factory\Schemas\InstanceSettings;
use Lkt\Factory\Schemas\Schema;
use Lkt\Instances\LktShoppingTax;
use Lkt\Instances\LktUser;
use Lkt\Shop\Enums\TaxTarget;
use Lkt\Shop\Enums\TaxType;

Schema::add(
    Schema::table('lkt_shopping_taxes', LaminimComponent::ShoppingTax->value)
        ->setInstanceSettings(InstanceSettings::simple(LktShoppingTax::class, 'Lkt\Generated', __DIR__ . '/../../Generated'))
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

            BooleanField::define('isActive', 'is_active')->setDefaultValue(false),
            StringField::i18n('name'),
            JSONField::associativeI18n('nameData', 'name'),

            ForeignKeyField::defineRelation(LaminimComponent::User->value, 'createdBy', 'created_by')->setOnReadIncludeOptions()->setDefaultValue([LktUser::class, 'getSignedInUserId']),
            ForeignKeyField::defineRelation(LaminimComponent::Currency->value, 'currency', 'currency_id')->setOnReadIncludeOptions(),
            ForeignKeyField::defineRelation(LaminimComponent::Country->value, 'country', 'country_id')->setOnReadIncludeOptions(),

            FloatField::define('taxAmount', 'tax_amount')->setDefaultValue(0),
            IntegerField::enumChoice(TaxType::class, 'taxType', 'tax_type')->setDefaultValue(TaxType::PercentualAdd->value),
            IntegerField::enumChoice(TaxTarget::class, 'taxTarget', 'tax_target')->setDefaultValue(TaxTarget::NaturalPerson->value),
        ])
        ->setRelatedAccessPolicy([
            'id' => 'value',
            'name' => 'label',
            'id',
            'isActive',
            'country',
            'taxAmount',
        ])
        ->addAccessPolicy('admin', [
            'id',
            'createdAt',
            'nameData',
            'isActive',
            'country',
            'currency',
            'taxAmount',
            'taxType',
            'taxTarget',
        ])
        ->addAccessPolicy('w:admin', [
            'nameData',
            'isActive',
            'country',
            'currency',
            'taxAmount',
            'taxType',
            'taxTarget',
        ])
        ->addAccessPolicy('admin-ls', [
            'id',
            'createdAt',
            'isActive',
            'country',
            'taxAmount',
        ])
);