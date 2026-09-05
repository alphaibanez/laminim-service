<?php

namespace Lkt\Config\Schemas;

use Lkt\Enums\LaminimComponent;
use Lkt\Factory\Schemas\Fields\BooleanField;
use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\ForeignKeyField;
use Lkt\Factory\Schemas\Fields\IntegerChoiceField;
use Lkt\Factory\Schemas\Fields\IntegerField;
use Lkt\Factory\Schemas\Fields\StringField;
use Lkt\Factory\Schemas\InstanceSettings;
use Lkt\Factory\Schemas\Schema;
use Lkt\Instances\LktAuthenticationLog;
use Lkt\Users\Enums\PerformedAuthAction;
use Lkt\Users\Enums\UserStatus;

Schema::add(
    Schema::table('lkt_authentication_logs', LaminimComponent::AuthenticationLog->value)
        ->setInstanceSettings(InstanceSettings::simple(LktAuthenticationLog::class, 'Lkt\Generated', __DIR__ . '/../../Generated'))

        ->setItemsPerPage(20)
        ->setCountableField('id')
        ->setFields([
            IntegerField::identifier('id'),

            DateTimeField::define('createdAt', 'created_at')
                ->setDefaultReadFormat('Y-m-d')
                ->setCurrentTimeStampAsDefaultValue(),

            DateTimeField::define('updatedAt', 'updated_at')
                ->setDefaultReadFormat('Y-m-d')
                ->setCurrentTimeStampAsDefaultValue()
                ->setCurrentTimeStampOnUpdate(),

            IntegerChoiceField::enumChoice(PerformedAuthAction::class, 'performedAction', 'performed_action'),
            StringField::define('attemptedCredential', 'attempted_credential'),
            StringField::define('attemptedPassword', 'attempted_password'),
            BooleanField::define('attemptedSuccessfully', 'attempted_successfully'),
            IntegerField::define('attemptsCounter', 'attempts_counter'),
            StringField::define('clientProtocol', 'client_protocol'),
            StringField::define('clientUserAgent', 'client_useragent'),
            StringField::define('clientIPAddress', 'client_ip_address'),
            StringField::define('clientOS', 'client_os'),
            StringField::define('clientBrowser', 'client_browser'),
            StringField::define('clientBrowserVersion', 'client_browser_version'),
            ForeignKeyField::defineRelation(LaminimComponent::User->value, 'user', 'user_id'),
            IntegerChoiceField::enumChoice(UserStatus::class, 'userStatus', 'user_status')->setDefaultValue(UserStatus::Undefined->value)
        ])

        ->addAccessPolicy('sign-in-history', [
            'id',
            'createdAt',
            'clientOS',
            'clientBrowser',
            'clientBrowserVersion',
        ])
);