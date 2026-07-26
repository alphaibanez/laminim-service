<?php

namespace Lkt\Config\Schemas;

use Lkt\Enums\LaminimComponent;
use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\ForeignKeyField;
use Lkt\Factory\Schemas\Fields\IdField;
use Lkt\Factory\Schemas\Fields\IntegerChoiceField;
use Lkt\Factory\Schemas\InstanceSettings;
use Lkt\Factory\Schemas\Schema;
use Lkt\Instances\LktPushDelivery;
use Lkt\Instances\LktPushDevice;
use Lkt\Instances\LktPushNotification;
use Lkt\PushNotifications\Enums\DeliveryStatus;

Schema::add(
    Schema::table('lkt_push_deliveries', LaminimComponent::PushDelivery->value)
        ->setInstanceSettings(
            InstanceSettings::define(LktPushDelivery::class)
                ->setNamespaceForGeneratedClass('Lkt\Generated')
                ->setWhereStoreGeneratedClass(__DIR__ . '/../../Generated')
        )
        ->addField(DateTimeField::define('createdAt', 'created_at')->setCurrentTimeStampAsDefaultValue())
        ->addField(DateTimeField::define('sentAt', 'sent_at'))
        ->addField(IdField::define('id'))
        ->addField(IntegerChoiceField::enumChoice(DeliveryStatus::class, 'status'))
        ->addField(ForeignKeyField::defineRelation(LktPushDevice::COMPONENT, 'device', 'device_id'))
        ->addField(ForeignKeyField::defineRelation(LktPushNotification::COMPONENT, 'notification', 'notification_id'))
);