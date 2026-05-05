<?php

namespace Lkt\Config\Schemas;

use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\FloatField;
use Lkt\Factory\Schemas\Fields\ForeignKeyField;
use Lkt\Factory\Schemas\Fields\IdField;
use Lkt\Factory\Schemas\Fields\IntegerChoiceField;
use Lkt\Factory\Schemas\Fields\StringField;
use Lkt\Factory\Schemas\InstanceSettings;
use Lkt\Factory\Schemas\Schema;
use Lkt\Instances\LktCurrency;
use Lkt\Instances\LktShoppingOrder;
use Lkt\Instances\LktUser;
use Lkt\Shop\Enums\OrderStatus;

Schema::add(
    Schema::table('lkt_shopping_orders', LktShoppingOrder::COMPONENT)

        ->setInstanceSettings(InstanceSettings::simple(LktShoppingOrder::class, 'Lkt\Generated', __DIR__ . '/../../Generated'))

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

        ->addField(IntegerChoiceField::enumChoice(OrderStatus::class, 'status'))
        ->addField(ForeignKeyField::defineRelation(LktUser::COMPONENT, 'user', 'user_id'))
        ->addField(ForeignKeyField::defineRelation(LktCurrency::COMPONENT, 'currency', 'currency_id'))
        ->addField(FloatField::define('subTotal', 'subtotal'))
        ->addField(FloatField::define('taxTotal', 'tax_total'))
        ->addField(FloatField::define('shippingTotal', 'shipping_total'))
        ->addField(FloatField::define('discountTotal', 'discount_total'))
        ->addField(FloatField::define('total', 'total'))
);