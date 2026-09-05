<?php

namespace Lkt\Config\Schemas;

use Lkt\Enums\LaminimComponent;
use Lkt\Factory\Schemas\Fields\AssocJSONField;
use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\IntegerChoiceField;
use Lkt\Factory\Schemas\Fields\IntegerField;
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
        ->addField(DateTimeField::define('createdAt', 'created_at')->setCurrentTimeStampAsDefaultValue())
        ->addField(IntegerField::identifier('id'))
        ->addField(StringField::define('name')->setIsI18nJson())
        ->addField(AssocJSONField::define('nameData', 'name')->setIsI18nJson())
        ->addField(StringField::define('description')->setIsI18nJson())
        ->addField(AssocJSONField::define('descriptionData', 'description')->setIsI18nJson())
        ->addField(AssocJSONField::define('payload', 'data'))
        ->addField(IntegerChoiceField::enumChoice(NotificationStatus::class, 'status'))
        ->addField(IntegerChoiceField::enumChoice(QueuePriority::class, 'priority'))
        ->addField(IntegerChoiceField::enumChoice(NotificationTargetType::class, 'targetType', 'target_type'))
);