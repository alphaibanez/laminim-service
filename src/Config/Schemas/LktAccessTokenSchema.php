<?php

namespace Lkt\Config\Schemas;

use Lkt\Enums\AccessTokenDuration;
use Lkt\Enums\AccessTokenPurpose;
use Lkt\Enums\LaminimComponent;
use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\ForeignKeyField;
use Lkt\Factory\Schemas\Fields\IntegerChoiceField;
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
        ->setCountableField('id')
        ->setComplexPrimaryKey(['user', 'purpose'])
        ->addField(
            DateTimeField::define('createdAt', 'created_at')
                ->setDefaultReadFormat('Y-m-d')
                ->setCurrentTimeStampAsDefaultValue()
        )
        ->addField(
            DateTimeField::define('expiresAt', 'expires_at')
                ->setDefaultReadFormat('Y-m-d')
                ->setCurrentTimeStampAsDefaultValue()
        )
        ->addField(IntegerChoiceField::enumChoice(AccessTokenDuration::class, 'duration'))
        ->addField(IntegerChoiceField::enumChoice(AccessTokenPurpose::class, 'purpose'))
        ->addField(ForeignKeyField::defineRelation(LaminimComponent::User->value, 'user', 'user_id'))
        ->addField(StringField::define('token'))
);