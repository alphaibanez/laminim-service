<?php

namespace Lkt\Config\Schemas;

use Lkt\Enums\LaminimComponent;
use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\EmailField;
use Lkt\Factory\Schemas\Fields\ForeignKeyField;
use Lkt\Factory\Schemas\Fields\HTMLField;
use Lkt\Factory\Schemas\Fields\IntegerChoiceField;
use Lkt\Factory\Schemas\Fields\IntegerField;
use Lkt\Factory\Schemas\Fields\StringField;
use Lkt\Factory\Schemas\InstanceSettings;
use Lkt\Factory\Schemas\Schema;
use Lkt\Instances\LktPendingMail;
use Lkt\Instances\LktUser;
use Lkt\Mailing\Enums\QueuePriority;

Schema::add(
    Schema::table('lkt_mailing_queue', LaminimComponent::PendingMail->value)
        ->setInstanceSettings(
            InstanceSettings::define(LktPendingMail::class)
                ->setNamespaceForGeneratedClass('Lkt\Generated')
                ->setWhereStoreGeneratedClass(__DIR__ . '/../../Generated')
        )
        ->setFields([
            IntegerField::identifier('id'),
            DateTimeField::define('createdAt', 'created_at')->setCurrentTimeStampAsDefaultValue(),
            ForeignKeyField::defineRelation(LaminimComponent::User->value, 'createdBy', 'created_by')->setOnReadIncludeOptions()->setDefaultValue([LktUser::class, 'getSignedInUserId']),
            EmailField::define('email'),
            StringField::define('subject'),
            HTMLField::define('message'),
            IntegerChoiceField::enumChoice(QueuePriority::class, 'priority')->setDefaultValue(QueuePriority::Medium->value),
        ])
);