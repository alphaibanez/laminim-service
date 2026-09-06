<?php

namespace Lkt\Config\Schemas;

use Lkt\Config\Settings\UserSettings;
use Lkt\Enums\LaminimComponent;
use Lkt\Factory\Schemas\Fields\BooleanField;
use Lkt\Factory\Schemas\Fields\ConcatField;
use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\EmailField;
use Lkt\Factory\Schemas\Fields\EncryptField;
use Lkt\Factory\Schemas\Fields\ForeignKeysField;
use Lkt\Factory\Schemas\Fields\IntegerField;
use Lkt\Factory\Schemas\Fields\RelatedField;
use Lkt\Factory\Schemas\Fields\StringField;
use Lkt\Factory\Schemas\InstanceSettings;
use Lkt\Factory\Schemas\Schema;
use Lkt\Instances\LktUser;
use Lkt\Locale\Locale;
use Lkt\Users\Enums\ThemeMode;
use Lkt\Users\Enums\UserStatus;

Schema::add(
    Schema::table('lkt_users', LaminimComponent::User->value)
        ->setInstanceSettings(
            InstanceSettings::define(LktUser::class)
                ->setNamespaceForGeneratedClass('Lkt\Generated')
                ->setWhereStoreGeneratedClass(__DIR__ . '/../../Generated')
        )
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

            IntegerField::enumChoice(UserStatus::class, 'status')->setDefaultValue(UserStatus::Active->value),
            StringField::define('firstName', 'firstname'),
            StringField::define('lastName', 'lastname'),
            ConcatField::concat('fullName', ['firstName', 'lastName'], ' '),
            ConcatField::concat('name', ['firstName', 'lastName'], ' '),
            EmailField::define('email'),
            EncryptField::sha256Hash(UserSettings::$passwordSecureSeed, 'password'),

            StringField::define('preferredLanguage', 'preferred_language')->setDefaultValue(function () {
                return trim(Locale::getLangCode());
            }),

            IntegerField::enumChoice(ThemeMode::class, 'preferredThemeMode', 'preferred_theme_mode'),
            StringField::define('credentialIdentifier', 'credential_id'),
            ForeignKeysField::defineRelation(LaminimComponent::UserRole->value, 'appRoles', 'app_roles'),
            ForeignKeysField::defineRelation(LaminimComponent::UserRole->value, 'adminRoles', 'admin_roles'),
            BooleanField::define('isAdministrator', 'is_administrator'),
            BooleanField::define('canReceivePushNotifications', 'can_receive_push_notifications')->setDefaultValue(true),
            BooleanField::define('canReceiveMailNotifications', 'can_receive_mail_notifications')->setDefaultValue(true),
            RelatedField::defineRelation(LaminimComponent::ShoppingCoupon->value, 'coupons', 'owned_by'),
        ])
        ->setRelatedAccessPolicy([
            'id' => 'value',
            'fullName' => 'label',
        ])
        ->addAccessPolicy('change-password', ['password'])
        ->addAccessPolicy('admin', [
            'id',
            'firstName',
            'lastName',
            'fullName',
            'email',
            'credentialIdentifier',
            'preferredLanguage',
            'preferredThemeMode',
            'appRoles',
            'adminRoles',
            'canReceivePushNotifications',
            'canReceiveMailNotifications',
            'coupons',
            'status',
        ])
        ->addAccessPolicy('create', [
            'id',
            'firstName',
            'lastName',
            'fullName',
            'email',
            'password',
            'status',
            'credentialIdentifier',
            'preferredLanguage',
            'preferredThemeMode',
            'appRoles',
            'adminRoles',
            'canReceivePushNotifications',
            'canReceiveMailNotifications',
        ])
);