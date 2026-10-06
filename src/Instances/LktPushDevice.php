<?php

namespace Lkt\Instances;

use Lkt\Factory\Schemas\Schema;
use Lkt\Generated\GeneratedLktPushDevice;
use Lkt\Generated\LktPushDeviceQueryBuilder;
use Lkt\PushNotifications\Enums\DevicePlatform;
use Lkt\PushNotifications\Enums\DeviceStatus;

class LktPushDevice extends GeneratedLktPushDevice
{
    const COMPONENT = 'lkt-push-device';

    public static function logUserSignIn(LktUser $user, string $deviceId, string|null $devicePlatform = null, string|null $appVersion = null)
    {
        $schema = Schema::get(static::COMPONENT);

        /** @var LktPushDeviceQueryBuilder $query */
        $query = $schema->getQueryBuilder();
        $query
            ->andTokenEqual($deviceId)
            ->andLastClaimedByUserIdEqual($user->getId());

        $ins = $schema->getOne($query);

        if (!$ins) {
            $appVersion = trim($appVersion);
            $devicePlatform = DevicePlatform::tryFrom(trim($devicePlatform));
            if (!$devicePlatform) $devicePlatform = DevicePlatform::Unknown;

            $ins = static::getInstance();
            $ins
                ->setToken($deviceId)
                ->setLastClaimedByUserIdId($user->getId())
                ->setCreatedAt(time())
                ->setCreatedById($user->getId())
                ->setAppVersion($appVersion)
                ->setCreatedAtAppVersion($appVersion)
                ->setPlatform($devicePlatform->value)
                ->setStatus(DeviceStatus::Active->value)
            ;
        }
    }
}