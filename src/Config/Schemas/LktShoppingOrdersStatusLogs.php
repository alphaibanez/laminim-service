<?php

namespace Lkt\Config\Schemas;

use Lkt\Enums\LaminimComponent;
use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\ForeignKeyField;
use Lkt\Factory\Schemas\Fields\IntegerField;
use Lkt\Factory\Schemas\InstanceSettings;
use Lkt\Factory\Schemas\Schema;
use Lkt\Instances\LktShoppingOrderStatusLog;
use Lkt\Shop\Enums\OrderStatus;

Schema::add(
    Schema::table('lkt_shopping_orders__status_logs', LaminimComponent::ShoppingOrderStatusLog->value)

        ->setInstanceSettings(InstanceSettings::simple(LktShoppingOrderStatusLog::class, 'Lkt\Generated', __DIR__ . '/../../Generated'))

        ->setItemsPerPage(20)
        ->setCountableField('id')
        ->setFields([
            IntegerField::identifier('id'),

            DateTimeField::define('createdAt', 'created_at')
                ->setDefaultReadFormat('Y-m-d')
                ->setCurrentTimeStampAsDefaultValue(),

            ForeignKeyField::defineRelation(LaminimComponent::ShoppingOrder->value, 'order', 'order_id'),
            IntegerField::enumChoice(OrderStatus::class, 'status'),
        ])
);