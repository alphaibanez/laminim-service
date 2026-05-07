<?php

namespace Lkt\Config\Schemas;

use Lkt\Factory\Schemas\Fields\BooleanField;
use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\FloatField;
use Lkt\Factory\Schemas\Fields\ForeignKeyField;
use Lkt\Factory\Schemas\Fields\IdField;
use Lkt\Factory\Schemas\Fields\IntegerChoiceField;
use Lkt\Factory\Schemas\InstanceSettings;
use Lkt\Factory\Schemas\Schema;
use Lkt\Instances\LktCurrency;
use Lkt\Instances\LktShoppingCoupon;
use Lkt\Instances\LktUser;
use Lkt\Shop\Enums\CouponDiscountType;
use Lkt\Shop\Enums\CouponType;

Schema::add(
    Schema::table('lkt_shopping_coupons', LktShoppingCoupon::COMPONENT)

        ->setInstanceSettings(InstanceSettings::simple(LktShoppingCoupon::class, 'Lkt\Generated', __DIR__ . '/../../Generated'))

        ->setItemsPerPage(20)
        ->setOwnershipField('creator')
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
        ->addField(ForeignKeyField::defineRelation(LktUser::COMPONENT, 'creator', 'created_by')->setDefaultValue([LktUser::class, 'getSignedInUserId']))

        ->addField(IntegerChoiceField::enumChoice(CouponType::class, 'type')->setDefaultValue(CouponType::Global))
        ->addField(IntegerChoiceField::enumChoice(CouponDiscountType::class, 'discountType', 'discount_type')->setDefaultValue(CouponDiscountType::Percent))
        ->addField(FloatField::define('value', 'value')->setDefaultValue(0))
        ->addField(ForeignKeyField::defineRelation(LktCurrency::COMPONENT, 'currency', 'currency_id'))

        ->addField(DateTimeField::define('startsAt', 'starts_at')->setDefaultReadFormat('Y-m-d'))
        ->addField(DateTimeField::define('endsAt', 'ends_at')->setDefaultReadFormat('Y-m-d'))

        ->addField(FloatField::define('usageLimit', 'usage_limit')->setDefaultValue(0))
        ->addField(FloatField::define('usageLimitPerUser', 'usage_limit_per_user')->setDefaultValue(0))
        ->addField(FloatField::define('minimumOrderAmount', 'minimum_order_amount')->setDefaultValue(0))
        ->addField(BooleanField::define('isActive', 'is_active')->setDefaultValue(false))
        ->addField(BooleanField::define('stackable', 'stackable')->setDefaultValue(false))
);