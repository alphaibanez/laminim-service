<?php

namespace Lkt\Config\Schemas;

use Lkt\Enums\LaminimComponent;
use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\ForeignKeyField;
use Lkt\Factory\Schemas\Fields\IntegerChoiceField;
use Lkt\Factory\Schemas\Fields\IntegerField;
use Lkt\Factory\Schemas\Fields\MethodGetterField;
use Lkt\Factory\Schemas\Fields\PivotField;
use Lkt\Factory\Schemas\Fields\PivotLeftIdField;
use Lkt\Factory\Schemas\Fields\PivotPositionField;
use Lkt\Factory\Schemas\Fields\PivotRightIdField;
use Lkt\Factory\Schemas\InstanceSettings;
use Lkt\Factory\Schemas\Schema;
use Lkt\Instances\LktShoppingOrderPivotSubscription;
use Lkt\Instances\LktShoppingSubscription;
use Lkt\Instances\LktUser;
use Lkt\Shop\Enums\SubscriptionStatus;

Schema::add(
    Schema::table('lkt_shopping_subscriptions', LaminimComponent::ShoppingSubscription->value)

        ->setInstanceSettings(InstanceSettings::simple(LktShoppingSubscription::class, 'Lkt\Generated', __DIR__ . '/../../Generated'))

        ->setItemsPerPage(20)
        ->setOwnershipField('user')
        ->setCountableField('id')
        ->addField(IntegerField::identifier('id'))
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
        ->addField(ForeignKeyField::defineRelation(LaminimComponent::User->value, 'user', 'user_id')->setDefaultValue([LktUser::class, 'getSignedInUserId'])->setOnReadIncludeOptions())

        ->addField(IntegerChoiceField::enumChoice(SubscriptionStatus::class, 'status')->setDefaultValue(SubscriptionStatus::Inactive->value))

        ->addField(MethodGetterField::define('getComponentIdAssociatedWebItemPublicName', 'webItemName'))
        ->addField(IntegerField::define('componentId', 'component_id'))
        ->addField(ForeignKeyField::define('product', 'product_id')->setDynamicComponentField('componentId')->setOnReadIncludeOptions())

        ->addField(DateTimeField::define('startsAt', 'starts_at')->setDefaultReadFormat('Y-m-d H:i:s')->setNullable())
        ->addField(DateTimeField::define('endsAt', 'ends_at')->setDefaultReadFormat('Y-m-d H:i:s')->setNullable())

        ->addField(PivotField::definePivot(LaminimComponent::ShoppingOrder->value, 'lkt_shopping_orders__subscriptions', 'orders', 'subscription_id', LaminimComponent::ShoppingOrderPivotShoppingSubscription->value)
            ->setPivotLeftIdField(PivotLeftIdField::defineRelation(LaminimComponent::ShoppingOrder->value, 'order', 'order_id'))
            ->setPivotRightIdField(PivotRightIdField::defineRelation(LaminimComponent::ShoppingSubscription->value, 'subscription', 'subscription_id'))
            ->setPivotPositionField(PivotPositionField::define('position'))
            ->setPivotInstanceConfig(LktShoppingOrderPivotSubscription::class, 'Lkt\Generated', __DIR__ . '/../../Generated')
        )

    ->setRelatedAccessPolicy([
        'id' => 'value',
        'id',
        'createdAt',
        'status',
        'user',
        'startsAt',
        'endsAt',
    ])

    ->addAccessPolicy('admin', [
        'id',
        'createdAt',
        'status',
        'user',
        'startsAt',
        'endsAt',
        'product',
        'webItemName',
    ])

    ->addAccessPolicy('w:admin', [
        'user',
        'status',
        'productId',
        'componentId',
        'startsAt',
        'endsAt',
    ])
);