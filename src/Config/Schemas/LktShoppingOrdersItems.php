<?php

namespace Lkt\Config\Schemas;

use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\FloatField;
use Lkt\Factory\Schemas\Fields\ForeignKeyField;
use Lkt\Factory\Schemas\Fields\IdField;
use Lkt\Factory\Schemas\Fields\IntegerField;
use Lkt\Factory\Schemas\Fields\StringField;
use Lkt\Factory\Schemas\InstanceSettings;
use Lkt\Factory\Schemas\Schema;
use Lkt\Instances\LktShoppingOrder;
use Lkt\Instances\LktShoppingOrderItem;

Schema::add(
    Schema::table('lkt_shopping_orders__items', LktShoppingOrderItem::COMPONENT)

        ->setInstanceSettings(InstanceSettings::simple(LktShoppingOrderItem::class, 'Lkt\Generated', __DIR__ . '/../../Generated'))

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
                ->setCurrentTimeStampOnUpdate()
        )

        ->addField(ForeignKeyField::defineRelation(LktShoppingOrder::COMPONENT, 'order', 'order_id'))
        ->addField(IntegerField::define('componentId', 'component_id'))
        ->addField(ForeignKeyField::define('product', 'product_id')->setDynamicComponentField('componentId'))

        ->addField(StringField::define('SKU', 'sku'))
        ->addField(StringField::define('name'))

        ->addField(FloatField::define('pricePerUnit', 'price_unit')->setDefaultValue(0))
        ->addField(IntegerField::define('quantity')->setDefaultValue(1))
        ->addField(FloatField::define('taxAmount', 'tax_amount')->setDefaultValue(0))
        ->addField(FloatField::define('discountAmount', 'discount_amount')->setDefaultValue(0))
        ->addField(FloatField::define('total')->setDefaultValue(0))

        ->setRelatedAccessPolicy([
            'id' => 'value',
            'name' => 'label',
        ])
);