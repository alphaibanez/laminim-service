<?php

namespace Lkt\Config\Schemas;

use Lkt\Enums\LaminimComponent;
use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\FloatField;
use Lkt\Factory\Schemas\Fields\ForeignKeyField;
use Lkt\Factory\Schemas\Fields\IntegerChoiceField;
use Lkt\Factory\Schemas\Fields\IntegerField;
use Lkt\Factory\Schemas\Fields\StringField;
use Lkt\Factory\Schemas\InstanceSettings;
use Lkt\Factory\Schemas\Schema;
use Lkt\Instances\LktShoppingOrderPayment;
use Lkt\Shop\Enums\PaymentMethod;
use Lkt\Shop\Enums\PaymentStatus;

Schema::add(
    Schema::table('lkt_shopping_orders__payments', LaminimComponent::ShoppingOrderPayment->value)
        ->setInstanceSettings(InstanceSettings::simple(LktShoppingOrderPayment::class, 'Lkt\Generated', __DIR__ . '/../../Generated'))
        ->setItemsPerPage(20)
        ->setCountableField('id')
        ->addField(IntegerField::identifier('id'))
        ->addField(
            DateTimeField::define('createdAt', 'created_at')
                ->setDefaultReadFormat('Y-m-d')
                ->setCurrentTimeStampAsDefaultValue()
        )
        ->addField(
            DateTimeField::define('paidAt', 'paid_at')
                ->setDefaultReadFormat('Y-m-d')
        )
        ->addField(IntegerChoiceField::enumChoice(PaymentStatus::class, 'status')->setDefaultValue(PaymentStatus::Pending->value))
        ->addField(IntegerChoiceField::enumChoice(PaymentMethod::class, 'paymentMethod', 'payment_method'))
        ->addField(ForeignKeyField::defineRelation(LaminimComponent::ShoppingOrder->value, 'order', 'order_id')->setOnReadIncludeOptions())
        ->addField(FloatField::define('amount')->setDefaultValue(0))
        ->addField(StringField::define('transactionID', 'transaction_id')->setDefaultValue(''))
        ->setRelatedAccessPolicy([
            'id' => 'value',
            'amount' => 'label',
            'id',
            'createdAt',
            'paidAt',
            'status',
            'paymentMethod',
            'amount',
            'transactionID',
        ])

        ->addAccessPolicy('admin', [
            'id', 'createdAt', 'status', 'order',
            'amount',
            'paymentMethod','transactionID'
        ])
);