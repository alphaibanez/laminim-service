<?php

namespace Lkt\Config\Schemas;

use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\ForeignKeyField;
use Lkt\Factory\Schemas\Fields\IdField;
use Lkt\Factory\Schemas\Fields\IntegerChoiceField;
use Lkt\Factory\Schemas\Fields\IntegerField;
use Lkt\Factory\Schemas\Fields\PivotField;
use Lkt\Factory\Schemas\Fields\PivotLeftIdField;
use Lkt\Factory\Schemas\Fields\PivotPositionField;
use Lkt\Factory\Schemas\Fields\PivotRightIdField;
use Lkt\Factory\Schemas\InstanceSettings;
use Lkt\Factory\Schemas\Schema;
use Lkt\Instances\LktShoppingOrder;
use Lkt\Instances\LktShoppingOrderPivotCoupon;
use Lkt\Instances\LktShoppingOrderPivotSubscription;
use Lkt\Instances\LktShoppingSubscription;
use Lkt\Instances\LktUser;
use Lkt\Shop\Enums\SubscriptionStatus;

Schema::add(
    Schema::table('lkt_shopping_subscriptions', LktShoppingSubscription::COMPONENT)

        ->setInstanceSettings(InstanceSettings::simple(LktShoppingSubscription::class, 'Lkt\Generated', __DIR__ . '/../../Generated'))

        ->setItemsPerPage(20)
        ->setOwnershipField('user')
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
        ->addField(ForeignKeyField::defineRelation(LktUser::COMPONENT, 'user', 'user_id')->setDefaultValue([LktUser::class, 'getSignedInUserId'])->setOnReadIncludeOptions())

        ->addField(IntegerChoiceField::enumChoice(SubscriptionStatus::class, 'status')->setDefaultValue(SubscriptionStatus::Inactive->value))

        ->addField(IntegerField::define('componentId', 'component_id'))
        ->addField(ForeignKeyField::define('product', 'product_id')->setDynamicComponentField('componentId'))

        ->addField(DateTimeField::define('startsAt', 'starts_at')->setDefaultReadFormat('Y-m-d H:i:s')->setNullable())
        ->addField(DateTimeField::define('endsAt', 'ends_at')->setDefaultReadFormat('Y-m-d H:i:s')->setNullable())

        ->addField(PivotField::definePivot(LktShoppingSubscription::COMPONENT, 'lkt_shopping_orders__subscriptions', 'orders', 'subscription_id', LktShoppingOrderPivotCoupon::COMPONENT)
            ->setPivotLeftIdField(PivotLeftIdField::defineRelation(LktShoppingOrder::COMPONENT, 'order', 'order_id'))
            ->setPivotRightIdField(PivotRightIdField::defineRelation(LktShoppingSubscription::COMPONENT, 'subscription', 'subscription_id'))
            ->setPivotPositionField(PivotPositionField::define('position'))
            ->setPivotInstanceConfig(LktShoppingOrderPivotSubscription::class, 'Lkt\Generated', __DIR__ . '/../../Generated')
        )

    ->setRelatedAccessPolicy([
        'id' => 'value',
        'id',
        'createdAt',
        'user',
        'startsAt',
        'endsAt',
    ])

    ->addAccessPolicy('admin', [
        'id',
        'createdAt',
        'user',
        'startsAt',
        'endsAt',
    ])

    ->addAccessPolicy('w:admin', [
        'user',
        'startsAt',
        'endsAt',
    ])
);