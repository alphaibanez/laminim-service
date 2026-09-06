<?php

namespace Lkt\Config\Schemas;

use Lkt\Enums\LaminimComponent;
use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\IntegerField;
use Lkt\Factory\Schemas\InstanceSettings;
use Lkt\Factory\Schemas\Schema;
use Lkt\Instances\LktPushDevice;
use Lkt\PushNotifications\Enums\DevicePlatform;

Schema::add(
    Schema::table('lkt_push_devices', LaminimComponent::PushDevice->value)
        ->setInstanceSettings(
            InstanceSettings::define(LktPushDevice::class)
                ->setNamespaceForGeneratedClass('Lkt\Generated')
                ->setWhereStoreGeneratedClass(__DIR__ . '/../../Generated')
        )
        ->setFields([
            IntegerField::identifier('id'),
            DateTimeField::define('createdAt', 'created_at')->setCurrentTimeStampAsDefaultValue(),
            IntegerField::enumChoice(DevicePlatform::class, 'platform')
        ])
);