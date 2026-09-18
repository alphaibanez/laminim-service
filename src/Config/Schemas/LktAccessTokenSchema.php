<?php

namespace Lkt\Config\Schemas;

use Lkt\Enums\AccessTokenDuration;
use Lkt\Enums\AccessTokenPurpose;
use Lkt\Enums\LaminimComponent;
use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\IntegerField;
use Lkt\Factory\Schemas\Fields\StringField;
use Lkt\Factory\Schemas\InstanceSettings;
use Lkt\Factory\Schemas\Schema;
use Lkt\Instances\LktAccessToken;

Schema::add(
    Schema::table('lkt_access_token', LaminimComponent::AccessToken->value)
        ->setInstanceSettings(
            InstanceSettings::define(LktAccessToken::class)
                ->setNamespaceForGeneratedClass('Lkt\Generated')
                ->setWhereStoreGeneratedClass(__DIR__ . '/../../Generated')
        )
        ->setItemsPerPage(20)
//        ->setComplexPrimaryKey(['user', 'purpose'])
        ->setFields([
            IntegerField::foreignKey(LaminimComponent::User->value, 'user', 'user_id')->setIsIdentifier(),
            IntegerField::enumChoice(AccessTokenPurpose::class, 'purpose')->setIsIdentifier(),
            IntegerField::enumChoice(AccessTokenDuration::class, 'duration'),

            DateTimeField::define('createdAt', 'created_at')
                ->setDefaultReadFormat('Y-m-d')
                ->setCurrentTimeStampAsDefaultValue(),

            DateTimeField::define('expiresAt', 'expires_at')
                ->setDefaultReadFormat('Y-m-d')
                ->setCurrentTimeStampAsDefaultValue(),

            StringField::define('token'),
        ])
);