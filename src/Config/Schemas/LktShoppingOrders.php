<?php

namespace Lkt\Config\Schemas;

use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\FloatField;
use Lkt\Factory\Schemas\Fields\ForeignKeyField;
use Lkt\Factory\Schemas\Fields\IdField;
use Lkt\Factory\Schemas\Fields\IntegerChoiceField;
use Lkt\Factory\Schemas\Fields\PivotField;
use Lkt\Factory\Schemas\Fields\PivotLeftIdField;
use Lkt\Factory\Schemas\Fields\PivotPositionField;
use Lkt\Factory\Schemas\Fields\PivotRightIdField;
use Lkt\Factory\Schemas\Fields\RelatedField;
use Lkt\Factory\Schemas\InstanceSettings;
use Lkt\Factory\Schemas\Schema;
use Lkt\Instances\LktCurrency;
use Lkt\Instances\LktShoppingCoupon;
use Lkt\Instances\LktShoppingOrder;
use Lkt\Instances\LktShoppingOrderItem;
use Lkt\Instances\LktShoppingOrderPayment;
use Lkt\Instances\LktShoppingOrderPivotCoupon;
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

        ->addField(IntegerChoiceField::enumChoice(OrderStatus::class, 'status')->setDefaultValue(OrderStatus::Pending))
        ->addField(ForeignKeyField::defineRelation(LktUser::COMPONENT, 'user', 'user_id')->setDefaultValue([LktUser::class, 'getSignedInUserId']))
        ->addField(ForeignKeyField::defineRelation(LktCurrency::COMPONENT, 'currency', 'currency_id'))
        ->addField(FloatField::define('subTotal', 'subtotal')->setDefaultValue(0))
        ->addField(FloatField::define('taxTotal', 'tax_total')->setDefaultValue(0))
        ->addField(FloatField::define('shippingTotal', 'shipping_total')->setDefaultValue(0))
        ->addField(FloatField::define('discountTotal', 'discount_total')->setDefaultValue(0))
        ->addField(FloatField::define('total', 'total')->setDefaultValue(0))
        ->addField(RelatedField::defineRelation(LktShoppingOrderItem::COMPONENT, 'items', 'order_id'))
        ->addField(RelatedField::defineRelation(LktShoppingOrderPayment::COMPONENT, 'payments', 'order_id'))

        ->addField(PivotField::definePivot(LktShoppingCoupon::COMPONENT, 'lkt_shopping_orders__coupons', 'coupons', 'order_id', LktShoppingOrderPivotCoupon::COMPONENT)
            ->setPivotLeftIdField(PivotLeftIdField::defineRelation(LktShoppingOrder::COMPONENT, 'order', 'order_id'))
            ->setPivotRightIdField(PivotRightIdField::defineRelation(LktShoppingCoupon::COMPONENT, 'coupon', 'coupon_id'))
            ->setPivotPositionField(PivotPositionField::define('position'))
            ->setPivotInstanceConfig(LktShoppingOrderPivotCoupon::class, 'Lkt\Generated', __DIR__ . '/../../Generated')
        )
);