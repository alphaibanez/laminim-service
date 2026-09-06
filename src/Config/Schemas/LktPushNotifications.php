<?php

namespace Lkt\Config\Schemas;

use Lkt\Enums\LaminimComponent;
use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\IntegerChoiceField;
use Lkt\Factory\Schemas\Fields\IntegerField;
use Lkt\Factory\Schemas\Fields\JSONField;
use Lkt\Factory\Schemas\Fields\StringField;
use Lkt\Factory\Schemas\InstanceSettings;
use Lkt\Factory\Schemas\Schema;
use Lkt\Instances\LktPushNotification;
use Lkt\Mailing\Enums\QueuePriority;
use Lkt\PushNotifications\Enums\NotificationStatus;
use Lkt\PushNotifications\Enums\NotificationTargetType;

Schema::add(
    Schema::table('lkt_push_notifications', LaminimComponent::PushNotification->value)
        ->setInstanceSettings(
            InstanceSettings::define(LktPushNotification::class)
                ->setNamespaceForGeneratedClass('Lkt\Generated')
                ->setWhereStoreGeneratedClass(__DIR__ . '/../../Generated')
        )
        ->setFields([
            IntegerField::identifier('id'),
            DateTimeField::define('createdAt', 'created_at')->setCurrentTimeStampAsDefaultValue(),
            StringField::i18n('name'),
            JSONField::associativeI18n('nameData', 'name'),
            StringField::i18n('description'),
            JSONField::associativeI18n('descriptionData', 'description'),
            JSONField::associative('payload', 'data'),
            IntegerChoiceField::enumChoice(NotificationStatus::class, 'status'),
            IntegerChoiceField::enumChoice(QueuePriority::class, 'priority'),
            IntegerChoiceField::enumChoice(NotificationTargetType::class, 'targetType', 'target_type'),
        ])
);