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
use Lkt\Instances\LktShoppingOrder;
use Lkt\Instances\LktShoppingOrderPayment;
use Lkt\Shop\Enums\PaymentMethod;
use Lkt\Shop\Enums\PaymentStatus;

Schema::add(
    Schema::table('lkt_shopping_orders__payments', LktShoppingOrderPayment::COMPONENT)
        ->setInstanceSettings(InstanceSettings::simple(LktShoppingOrderPayment::class, 'Lkt\Generated', __DIR__ . '/../../Generated'))
        ->setItemsPerPage(20)
        ->setCountableField('id')
        ->addField(IdField::define('id'))
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
        ->addField(ForeignKeyField::defineRelation(LktShoppingOrder::COMPONENT, 'order', 'order_id'))
        ->addField(FloatField::define('amount')->setDefaultValue(0))
        ->addField(StringField::define('transactionID', 'transaction_id')->setDefaultValue(''))
        ->setRelatedAccessPolicy([
            'id' => 'value',
            'amount' => 'label',
        ])
);