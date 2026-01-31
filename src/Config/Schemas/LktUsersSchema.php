<?php

namespace Lkt\Users\Config\Schemas;

use Lkt\Factory\Schemas\Fields\BooleanField;
use Lkt\Factory\Schemas\Fields\ConcatField;
use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\EmailField;
use Lkt\Factory\Schemas\Fields\ForeignKeysField;
use Lkt\Factory\Schemas\Fields\IdField;
use Lkt\Factory\Schemas\Fields\IntegerChoiceField;
use Lkt\Factory\Schemas\Fields\StringField;
use Lkt\Factory\Schemas\InstanceSettings;
use Lkt\Factory\Schemas\Schema;
use Lkt\Instances\LktUser;
use Lkt\Instances\LktUserRole;
use Lkt\Locale\Locale;
use Lkt\Users\Enums\ThemeMode;
use Lkt\Users\Enums\UserStatus;

Schema::add(
    Schema::table('lkt_users', LktUser::COMPONENT)
        ->setInstanceSettings(
            InstanceSettings::define(LktUser::class)
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
        )
        ->addField(IntegerChoiceField::enumChoice(UserStatus::class,'status')->setDefaultValue(UserStatus::Active->value))
        ->addField(StringField::define('firstName', 'firstname'))
        ->addField(StringField::define('lastName', 'lastname'))
        ->addField(ConcatField::concat('fullName', ['firstName', 'lastName'], ' '))
        ->addField(ConcatField::concat('name', ['firstName', 'lastName'], ' '))
        ->addField(EmailField::define('email'))
        ->addField(StringField::define('password'))
        ->addField(StringField::define('preferredLanguage', 'preferred_language')->setDefaultValue(function (){
            return trim(Locale::getLangCode());
        }))
        ->addField(IntegerChoiceField::enumChoice(ThemeMode::class,'preferredThemeMode', 'preferred_theme_mode'))
        ->addField(StringField::define('credentialIdentifier', 'credential_id'))
        ->addField(ForeignKeysField::defineRelation(LktUserRole::COMPONENT, 'appRoles', 'app_roles'))
        ->addField(ForeignKeysField::defineRelation(LktUserRole::COMPONENT, 'adminRoles', 'admin_roles'))
        ->addField(StringField::define('sessionToken', 'session_token'))
        ->addField(BooleanField::define('isAdministrator', 'is_administrator'))
        ->addField(BooleanField::define('canReceivePushNotifications', 'can_receive_push_notifications')->setDefaultValue(true))
        ->addField(BooleanField::define('canReceiveMailNotifications', 'can_receive_mail_notifications')->setDefaultValue(true))
        ->addAccessPolicy('change-password', ['password'])
);