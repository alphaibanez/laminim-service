<?php

namespace Lkt\Mailing\Config\Schemas;

use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\EmailField;
use Lkt\Factory\Schemas\Fields\HTMLField;
use Lkt\Factory\Schemas\Fields\IdField;
use Lkt\Factory\Schemas\Fields\IntegerChoiceField;
use Lkt\Factory\Schemas\Fields\StringField;
use Lkt\Factory\Schemas\InstanceSettings;
use Lkt\Factory\Schemas\Schema;
use Lkt\Instances\LktPendingMail;
use Lkt\Mailing\Enums\QueuePriority;

Schema::add(
    Schema::table('lkt_mailing_queue', LktPendingMail::COMPONENT)
        ->setInstanceSettings(
            InstanceSettings::define(LktPendingMail::class)
                ->setNamespaceForGeneratedClass('Lkt\Generated')
                ->setWhereStoreGeneratedClass(__DIR__ . '/../../Generated')
        )
        ->addField(DateTimeField::define('createdAt', 'created_at')->setCurrentTimeStampAsDefaultValue())
        ->addField(IdField::define('id'))
        ->addField(EmailField::define('email'))
        ->addField(StringField::define('subject'))
        ->addField(HTMLField::define('message'))
        ->addField(IntegerChoiceField::enumChoice(QueuePriority::class, 'priority'))
);