<?php

namespace Lkt\Config\Schemas;

use Lkt\Enums\LaminimComponent;
use Lkt\Factory\Schemas\Fields\BooleanField;
use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\FloatField;
use Lkt\Factory\Schemas\Fields\ForeignKeyField;
use Lkt\Factory\Schemas\Fields\IntegerField;
use Lkt\Factory\Schemas\Fields\JSONField;
use Lkt\Factory\Schemas\Fields\PivotField;
use Lkt\Factory\Schemas\Fields\PivotLeftIdField;
use Lkt\Factory\Schemas\Fields\PivotPositionField;
use Lkt\Factory\Schemas\Fields\PivotRightIdField;
use Lkt\Factory\Schemas\Fields\StringField;
use Lkt\Factory\Schemas\InstanceSettings;
use Lkt\Factory\Schemas\Schema;
use Lkt\Instances\LktShoppingCoupon;
use Lkt\Instances\LktShoppingOrderPivotCoupon;
use Lkt\Instances\LktUser;
use Lkt\Shop\Enums\CouponDiscountType;
use Lkt\Shop\Enums\CouponType;

Schema::add(
    Schema::table('lkt_shopping_coupons', LaminimComponent::ShoppingCoupon->value)

        ->setInstanceSettings(InstanceSettings::simple(LktShoppingCoupon::class, 'Lkt\Generated', __DIR__ . '/../../Generated'))

        ->setItemsPerPage(20)
        ->setOwnershipField('owner')
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

            ForeignKeyField::defineRelation(LaminimComponent::User->value, 'creator', 'created_by')->setDefaultValue([LktUser::class, 'getSignedInUserId'])->setOnReadIncludeOptions(),
            ForeignKeyField::defineRelation(LaminimComponent::User->value, 'owner', 'owned_by')->setOnReadIncludeOptions(),

            StringField::define('code')->setIsUnique(),

            StringField::i18n('name'),
            JSONField::associativeI18n('nameData', 'name'),
            IntegerField::enumChoice(CouponType::class, 'type')->setDefaultValue(CouponType::Global),
            IntegerField::enumChoice(CouponDiscountType::class, 'discountType', 'discount_type')->setDefaultValue(CouponDiscountType::Percent),
            FloatField::define('value', 'value')->setDefaultValue(0),
            ForeignKeyField::defineRelation(LaminimComponent::Currency->value, 'currency', 'currency_id'),
            DateTimeField::define('startsAt', 'starts_at')->setDefaultReadFormat('Y-m-d H:i:s')->setNullable(),
            DateTimeField::define('endsAt', 'ends_at')->setDefaultReadFormat('Y-m-d H:i:s')->setNullable(),

            FloatField::define('usageLimit', 'usage_limit')->setDefaultValue(0),
            FloatField::define('usageLimitPerUser', 'usage_limit_per_user')->setDefaultValue(0),
            FloatField::define('minimumOrderAmount', 'minimum_order_amount')->setDefaultValue(0),
            BooleanField::define('isActive', 'is_active')->setDefaultValue(false),
            BooleanField::define('stackable', 'stackable')->setDefaultValue(false),

            PivotField::definePivot(LaminimComponent::ShoppingCoupon->value, 'lkt_shopping_orders__coupons', 'orders', 'coupon_id', LaminimComponent::ShoppingOrderPivotShoppingCoupon->value)
                ->setPivotLeftIdField(PivotLeftIdField::defineRelation(LaminimComponent::ShoppingOrder->value, 'order', 'order_id'))
                ->setPivotRightIdField(PivotRightIdField::defineRelation(LaminimComponent::ShoppingCoupon->value, 'coupon', 'coupon_id'))
                ->setPivotPositionField(PivotPositionField::define('position'))
                ->setPivotInstanceConfig(LktShoppingOrderPivotCoupon::class, 'Lkt\Generated', __DIR__ . '/../../Generated')

        ])

    ->setRelatedAccessPolicy([
        'id' => 'value',
        'id',
        'createdAt',
        'nameData',
        'code',
    ])

    ->addAccessPolicy('admin', [
        'id',
        'createdAt',
        'creator',
        'owner',
        'nameData',
        'code',
        'type',
        'discountType',
        'value',
        'currency',
        'startsAt',
        'endsAt',
        'usageLimit',
        'usageLimitPerUser',
        'minimumOrderAmount',
        'isActive',
        'stackable',
    ])

    ->addAccessPolicy('w:admin', [
        'owner',
        'nameData',
        'code',
        'type',
        'discountType',
        'value',
        'currency',
        'startsAt',
        'endsAt',
        'usageLimit',
        'usageLimitPerUser',
        'minimumOrderAmount',
        'isActive',
        'stackable',
    ])

    ->addAccessPolicy('admin-opt', [
        'id' => 'value',
        'id',
        'createdAt',
        'creator',
        'owner',
        'nameData',
        'code',
        'type',
        'discountType',
        'value',
        'currency',
        'startsAt',
        'endsAt',
        'usageLimit',
        'usageLimitPerUser',
        'minimumOrderAmount',
        'isActive',
        'stackable',
    ])

    ->addAccessPolicy('redeem', [
        'id',
        'createdAt',
        'owner',
        'name',
        'code',
        'type',
        'discountType',
        'value',
        'currency',
        'startsAt',
        'endsAt',
        'usageLimit',
        'usageLimitPerUser',
        'minimumOrderAmount',
        'isActive',
        'stackable',
    ])
);