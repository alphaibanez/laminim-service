<?php

namespace Lkt\Config\Schemas;

use Lkt\Enums\LaminimComponent;
use Lkt\Factory\Schemas\Fields\BooleanField;
use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\FloatField;
use Lkt\Factory\Schemas\Fields\ForeignKeyField;
use Lkt\Factory\Schemas\Fields\IntegerChoiceField;
use Lkt\Factory\Schemas\Fields\IntegerField;
use Lkt\Factory\Schemas\Fields\MethodGetterField;
use Lkt\Factory\Schemas\InstanceSettings;
use Lkt\Factory\Schemas\Schema;
use Lkt\Instances\LktShoppingPrice;
use Lkt\Shop\Enums\PriceCriteria;
use Lkt\Shop\Enums\PriceType;

Schema::add(
    Schema::table('lkt_shopping_prices', LaminimComponent::ShoppingPrice->value)

        ->setInstanceSettings(InstanceSettings::simple(LktShoppingPrice::class, 'Lkt\Generated', __DIR__ . '/../../Generated'))

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
            ForeignKeyField::defineRelation(LaminimComponent::Country->value, 'country', 'country_id')->setOnReadIncludeOptions(),
            ForeignKeyField::defineRelation(LaminimComponent::Currency->value, 'currency', 'currency_id')->setOnReadIncludeOptions(),
            ForeignKeyField::defineRelation(LaminimComponent::ShoppingTax->value, 'shoppingTax', 'shopping_tax_id')->setOnReadIncludeOptions(),
            IntegerField::define('componentId', 'component_id'),
            ForeignKeyField::define('product', 'product_id')->setDynamicComponentField('componentId')->setOnReadIncludeOptions(),
            FloatField::define('pricePerUnit', 'price_unit')->setDefaultValue(0),
            FloatField::define('taxAmount', 'tax_amount')->setDefaultValue(0),
            MethodGetterField::define('getFinalPricePerUnit', 'finalPricePerUnit'),
            IntegerChoiceField::enumChoice(PriceType::class, 'type', 'price_type')->setDefaultValue(PriceType::Override->value),
            IntegerChoiceField::enumChoice(PriceCriteria::class, 'attachedCriteria', 'attached_criteria')->setDefaultValue(PriceCriteria::ByCountry->value),
        ])

        ->setRelatedAccessPolicy([
            'id' => 'value',
            'id',
            'isActive',
            'country',
            'currency',
            'pricePerUnit',
            'taxAmount',
            'type',
            'attachedCriteria',
            'product',
            'webItemName',
        ])

        ->addAccessPolicy('admin', [
            'id',
            'createdAt',
            'isActive',
            'country',
            'currency',
            'pricePerUnit',
            'taxAmount',
            'type',
            'attachedCriteria',
            'product',
            'webItemName',
            'shoppingTax',
            'getFinalPricePerUnit',
        ])

        ->addAccessPolicy('w:admin', [
            'isActive',
            'countryId',
            'currencyId',
            'productId',
            'componentId',
            'pricePerUnit',
            'taxAmount',
            'type',
            'attachedCriteria',
            'shoppingTax',
        ])

        ->addAccessPolicy('admin-ls', [
            'id',
            'createdAt',
            'isActive',
            'country',
            'currency',
            'pricePerUnit',
            'taxAmount',
            'type',
            'attachedCriteria',
            'product',
            'webItemName',
        ])
);