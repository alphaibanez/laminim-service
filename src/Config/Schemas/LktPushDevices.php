<?php

namespace Lkt\Config\Schemas;

use Lkt\Enums\LaminimComponent;
use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\IntegerField;
use Lkt\Factory\Schemas\Fields\StringField;
use Lkt\Factory\Schemas\InstanceSettings;
use Lkt\Factory\Schemas\Schema;
use Lkt\Instances\LktPushDevice;
use Lkt\Instances\LktUser;
use Lkt\PushNotifications\Enums\DevicePlatform;
use Lkt\PushNotifications\Enums\DeviceStatus;

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
            IntegerField::foreignKey(LaminimComponent::User->value, 'createdBy', 'created_by')->setDefaultValue([LktUser::class, 'getSignedInUserId']),
            IntegerField::foreignKey(LaminimComponent::User->value, 'lastClaimedByUserId', 'latest_claimed_by_user_id')->setDefaultValue([LktUser::class, 'getSignedInUserId']),
            IntegerField::enumChoice(DevicePlatform::class, 'platform')->setDefaultValue(DevicePlatform::Unknown->value),
            IntegerField::enumChoice(DeviceStatus::class, 'status')->setDefaultValue(DeviceStatus::Active->value),
            StringField::define('createdAtAppVersion', 'created_at_app_version'),
            StringField::define('appVersion', 'app_version'),
            StringField::define('token'),
        ])
);