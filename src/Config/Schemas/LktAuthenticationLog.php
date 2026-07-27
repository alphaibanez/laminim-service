<?php

namespace Lkt\Config\Schemas;

use Lkt\Enums\LaminimComponent;
use Lkt\Factory\Schemas\Fields\BooleanField;
use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\ForeignKeyField;
use Lkt\Factory\Schemas\Fields\IdField;
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
        ->setInstanceSettings(
            InstanceSettings::define(LktAuthenticationLog::class)
                ->setNamespaceForGeneratedClass('Lkt\Generated')
                ->setWhereStoreGeneratedClass(__DIR__ . '/../../Generated')
        )
        ->setItemsPerPage(20)
        ->setCountableField('id')
        ->addField(IdField::define('id'))
        ->addField(
            DateTimeField::define('createdAt', 'created_at')
                ->setDefaultReadFormat('Y-m-d')
                ->setCurrentTimeStampAsDefaultValue()
        )
        ->addField(
            DateTimeField::define('updatedAt', 'updated_at')
                ->setDefaultReadFormat('Y-m-d')
                ->setCurrentTimeStampAsDefaultValue()
                ->setCurrentTimeStampOnUpdate()
        )
        ->addField(IntegerChoiceField::enumChoice(PerformedAuthAction::class, 'performedAction', 'performed_action'))
        ->addField(StringField::define('attemptedCredential', 'attempted_credential'))
        ->addField(StringField::define('attemptedPassword', 'attempted_password'))
        ->addField(BooleanField::define('attemptedSuccessfully', 'attempted_successfully'))
        ->addField(IntegerField::define('attemptsCounter', 'attempts_counter'))
        ->addField(StringField::define('clientProtocol', 'client_protocol'))
        ->addField(StringField::define('clientUserAgent', 'client_useragent'))
        ->addField(StringField::define('clientIPAddress', 'client_ip_address'))
        ->addField(StringField::define('clientOS', 'client_os'))
        ->addField(StringField::define('clientBrowser', 'client_browser'))
        ->addField(StringField::define('clientBrowserVersion', 'client_browser_version'))
        ->addField(ForeignKeyField::defineRelation(LaminimComponent::User->value, 'user', 'user_id'))
        ->addField(IntegerChoiceField::enumChoice(UserStatus::class, 'userStatus', 'user_status')
            ->setDefaultValue(UserStatus::Undefined->value))

        ->addAccessPolicy('sign-in-history', [
            'id',
            'createdAt',
            'clientOS',
            'clientBrowser',
            'clientBrowserVersion',
        ])
);