<?php

namespace Lkt\Config\Schemas;

use Lkt\Enums\LaminimComponent;
use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\IntegerChoiceField;
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
        ->addField(DateTimeField::define('createdAt', 'created_at')->setCurrentTimeStampAsDefaultValue())
        ->addField(IntegerField::identifier('id'))
        ->addField(IntegerChoiceField::enumChoice(DevicePlatform::class, 'platform'))
);