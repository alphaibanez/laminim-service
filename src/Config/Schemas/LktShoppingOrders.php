<?php

namespace Lkt\Config\Schemas;

use Lkt\Enums\LaminimComponent;
use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\FloatField;
use Lkt\Factory\Schemas\Fields\ForeignKeyField;
use Lkt\Factory\Schemas\Fields\IntegerField;
use Lkt\Factory\Schemas\Fields\PivotField;
use Lkt\Factory\Schemas\Fields\PivotLeftIdField;
use Lkt\Factory\Schemas\Fields\PivotPositionField;
use Lkt\Factory\Schemas\Fields\PivotRightIdField;
use Lkt\Factory\Schemas\Fields\RelatedField;
use Lkt\Factory\Schemas\InstanceSettings;
use Lkt\Factory\Schemas\Schema;
use Lkt\Instances\LktShoppingOrder;
use Lkt\Instances\LktShoppingOrderPivotCoupon;
use Lkt\Instances\LktShoppingOrderPivotSubscription;
use Lkt\Instances\LktUser;
use Lkt\Shop\Enums\OrderStatus;

Schema::add(
    Schema::table('lkt_shopping_orders', LaminimComponent::ShoppingOrder->value)

        ->setInstanceSettings(InstanceSettings::simple(LktShoppingOrder::class, 'Lkt\Generated', __DIR__ . '/../../Generated'))

        ->setItemsPerPage(20)
        ->setCountableField('id')
        ->setFields([
            IntegerField::identifier('id'),

            DateTimeField::define('createdAt', 'created_at')
                ->setDefaultReadFormat('Y-m-d H:i:s')
                ->setCurrentTimeStampAsDefaultValue(),


            DateTimeField::define('updatedAt', 'updated_at')
                ->setDefaultReadFormat('Y-m-d H:i:s')
                ->setCurrentTimeStampAsDefaultValue()
                ->setCurrentTimeStampOnUpdate(),

            IntegerField::enumChoice(OrderStatus::class, 'status')->setDefaultValue(OrderStatus::Pending),
            ForeignKeyField::defineRelation(LaminimComponent::User->value, 'user', 'user_id')->setDefaultValue([LktUser::class, 'getSignedInUserId'])->setOnReadIncludeOptions(),
            ForeignKeyField::defineRelation(LaminimComponent::Currency->value, 'currency', 'currency_id'),
            FloatField::define('subTotal', 'subtotal')->setDefaultValue(0),
            FloatField::define('taxTotal', 'tax_total')->setDefaultValue(0),
            FloatField::define('shippingTotal', 'shipping_total')->setDefaultValue(0),
            FloatField::define('discountTotal', 'discount_total')->setDefaultValue(0),
            FloatField::define('total', 'total')->setDefaultValue(0),
            RelatedField::defineRelation(LaminimComponent::ShoppingOrderItem->value, 'items', 'order_id'),
            RelatedField::defineRelation(LaminimComponent::ShoppingOrderPayment->value, 'payments', 'order_id'),

            PivotField::definePivot(LaminimComponent::ShoppingCoupon->value, 'lkt_shopping_orders__coupons', 'coupons', 'order_id', LaminimComponent::ShoppingOrderPivotShoppingCoupon->value)
                ->setPivotLeftIdField(PivotLeftIdField::defineRelation(LaminimComponent::ShoppingOrder->value, 'order', 'order_id'))
                ->setPivotRightIdField(PivotRightIdField::defineRelation(LaminimComponent::ShoppingCoupon->value, 'coupon', 'coupon_id'))
                ->setPivotPositionField(PivotPositionField::define('position'))
                ->setPivotInstanceConfig(LktShoppingOrderPivotCoupon::class, 'Lkt\Generated', __DIR__ . '/../../Generated'),

            PivotField::definePivot(LaminimComponent::ShoppingSubscription->value, 'lkt_shopping_orders__subscriptions', 'subscriptions', 'order_id', LaminimComponent::ShoppingOrderPivotShoppingSubscription->value)
                ->setPivotLeftIdField(PivotLeftIdField::defineRelation(LaminimComponent::ShoppingOrder->value, 'order', 'order_id'))
                ->setPivotRightIdField(PivotRightIdField::defineRelation(LaminimComponent::ShoppingSubscription->value, 'subscription', 'subscription_id'))
                ->setPivotPositionField(PivotPositionField::define('position'))
                ->setPivotInstanceConfig(LktShoppingOrderPivotSubscription::class, 'Lkt\Generated', __DIR__ . '/../../Generated')
        ])

        ->addAccessPolicy('admin-ls', [
            'id', 'createdAt', 'status', 'user',
            'total'
        ])

        ->addAccessPolicy('admin', [
            'id', 'createdAt', 'status', 'user',
            'total','payments'
        ])

        ->addAccessPolicy('w:admin', [
            'id', 'status',
        ])
);