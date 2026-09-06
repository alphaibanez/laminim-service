<?php

namespace Lkt\Config\Schemas;

use Lkt\Enums\LaminimComponent;
use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\ForeignKeyField;
use Lkt\Factory\Schemas\Fields\IntegerChoiceField;
use Lkt\Factory\Schemas\Fields\IntegerField;
use Lkt\Factory\Schemas\InstanceSettings;
use Lkt\Factory\Schemas\Schema;
use Lkt\Instances\LktPushDelivery;
use Lkt\PushNotifications\Enums\DeliveryStatus;

Schema::add(
    Schema::table('lkt_push_deliveries', LaminimComponent::PushDelivery->value)
        ->setInstanceSettings(
            InstanceSettings::define(LktPushDelivery::class)
                ->setNamespaceForGeneratedClass('Lkt\Generated')
                ->setWhereStoreGeneratedClass(__DIR__ . '/../../Generated')
        )
        ->setFields([
            IntegerField::identifier('id'),
            DateTimeField::define('createdAt', 'created_at')->setCurrentTimeStampAsDefaultValue(),
            DateTimeField::define('sentAt', 'sent_at'),
            IntegerChoiceField::enumChoice(DeliveryStatus::class, 'status'),
            ForeignKeyField::defineRelation(LaminimComponent::PushDevice->value, 'device', 'device_id'),
            ForeignKeyField::defineRelation(LaminimComponent::PushNotification->value, 'notification', 'notification_id')
        ])
);