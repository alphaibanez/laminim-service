<?php

namespace Lkt\Config\Schemas;

use Lkt\Enums\LaminimComponent;
use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\FloatField;
use Lkt\Factory\Schemas\Fields\ForeignKeyField;
use Lkt\Factory\Schemas\Fields\IntegerField;
use Lkt\Factory\Schemas\Fields\StringField;
use Lkt\Factory\Schemas\InstanceSettings;
use Lkt\Factory\Schemas\Schema;
use Lkt\Instances\LktShoppingOrderItem;

Schema::add(
    Schema::table('lkt_shopping_orders__items', LaminimComponent::ShoppingOrderItem->value)
        ->setInstanceSettings(InstanceSettings::simple(LktShoppingOrderItem::class, 'Lkt\Generated', __DIR__ . '/../../Generated'))
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

            ForeignKeyField::defineRelation(LaminimComponent::ShoppingOrder->value, 'order', 'order_id'),
            IntegerField::define('componentId', 'component_id'),
            ForeignKeyField::define('product', 'product_id')->setDynamicComponentField('componentId')->setOnReadIncludeOptions(),
            StringField::define('SKU', 'sku'),
            StringField::define('name'),
            FloatField::define('pricePerUnit', 'price_unit')->setDefaultValue(0),
            IntegerField::define('quantity')->setDefaultValue(1),
            FloatField::define('taxAmount', 'tax_amount')->setDefaultValue(0),
            FloatField::define('discountAmount', 'discount_amount')->setDefaultValue(0),
            FloatField::define('total')->setDefaultValue(0),
        ])
        ->setRelatedAccessPolicy([
            'id' => 'value',
            'name' => 'label',
        ])
);