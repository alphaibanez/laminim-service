<?php

namespace Lkt\Config\Schemas;

use Lkt\Factory\Schemas\Fields\AssocJSONField;
use Lkt\Factory\Schemas\Fields\BooleanField;
use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\FloatField;
use Lkt\Factory\Schemas\Fields\ForeignKeyField;
use Lkt\Factory\Schemas\Fields\IdField;
use Lkt\Factory\Schemas\Fields\IntegerChoiceField;
use Lkt\Factory\Schemas\Fields\PivotField;
use Lkt\Factory\Schemas\Fields\PivotLeftIdField;
use Lkt\Factory\Schemas\Fields\PivotPositionField;
use Lkt\Factory\Schemas\Fields\PivotRightIdField;
use Lkt\Factory\Schemas\Fields\StringField;
use Lkt\Factory\Schemas\InstanceSettings;
use Lkt\Factory\Schemas\Schema;
use Lkt\Instances\LktCurrency;
use Lkt\Instances\LktShoppingCoupon;
use Lkt\Instances\LktShoppingOrder;
use Lkt\Instances\LktShoppingOrderPivotCoupon;
use Lkt\Instances\LktUser;
use Lkt\Shop\Enums\CouponDiscountType;
use Lkt\Shop\Enums\CouponType;

Schema::add(
    Schema::table('lkt_shopping_coupons', LktShoppingCoupon::COMPONENT)

        ->setInstanceSettings(InstanceSettings::simple(LktShoppingCoupon::class, 'Lkt\Generated', __DIR__ . '/../../Generated'))

        ->setItemsPerPage(20)
        ->setOwnershipField('owner')
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
        ->addField(ForeignKeyField::defineRelation(LktUser::COMPONENT, 'creator', 'created_by')->setDefaultValue([LktUser::class, 'getSignedInUserId'])->setOnReadIncludeOptions())
        ->addField(ForeignKeyField::defineRelation(LktUser::COMPONENT, 'owner', 'owned_by')->setOnReadIncludeOptions())

        ->addField(StringField::define('code')->setIsUnique())

        ->addField(StringField::define('name')->setIsI18nJson())
        ->addField(AssocJSONField::define('nameData', 'name')->setIsI18nJson())

        ->addField(IntegerChoiceField::enumChoice(CouponType::class, 'type')->setDefaultValue(CouponType::Global))
        ->addField(IntegerChoiceField::enumChoice(CouponDiscountType::class, 'discountType', 'discount_type')->setDefaultValue(CouponDiscountType::Percent))
        ->addField(FloatField::define('value', 'value')->setDefaultValue(0))
        ->addField(ForeignKeyField::defineRelation(LktCurrency::COMPONENT, 'currency', 'currency_id'))

        ->addField(DateTimeField::define('startsAt', 'starts_at')->setDefaultReadFormat('Y-m-d H:i:s'))
        ->addField(DateTimeField::define('endsAt', 'ends_at')->setDefaultReadFormat('Y-m-d H:i:s'))

        ->addField(FloatField::define('usageLimit', 'usage_limit')->setDefaultValue(0))
        ->addField(FloatField::define('usageLimitPerUser', 'usage_limit_per_user')->setDefaultValue(0))
        ->addField(FloatField::define('minimumOrderAmount', 'minimum_order_amount')->setDefaultValue(0))
        ->addField(BooleanField::define('isActive', 'is_active')->setDefaultValue(false))
        ->addField(BooleanField::define('stackable', 'stackable')->setDefaultValue(false))

        ->addField(PivotField::definePivot(LktShoppingCoupon::COMPONENT, 'lkt_shopping_orders__coupons', 'orders', 'coupon_id', LktShoppingOrderPivotCoupon::COMPONENT)
            ->setPivotLeftIdField(PivotLeftIdField::defineRelation(LktShoppingOrder::COMPONENT, 'order', 'order_id'))
            ->setPivotRightIdField(PivotRightIdField::defineRelation(LktShoppingCoupon::COMPONENT, 'coupon', 'coupon_id'))
            ->setPivotPositionField(PivotPositionField::define('position'))
            ->setPivotInstanceConfig(LktShoppingOrderPivotCoupon::class, 'Lkt\Generated', __DIR__ . '/../../Generated')
        )

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