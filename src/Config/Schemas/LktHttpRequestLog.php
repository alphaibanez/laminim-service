<?php

namespace Lkt\Config\Schemas;

use Lkt\Enums\LaminimComponent;
use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\ForeignKeyField;
use Lkt\Factory\Schemas\Fields\IntegerField;
use Lkt\Factory\Schemas\Fields\JSONField;
use Lkt\Factory\Schemas\Fields\MethodGetterField;
use Lkt\Factory\Schemas\Fields\StringField;
use Lkt\Factory\Schemas\InstanceSettings;
use Lkt\Factory\Schemas\Schema;
use Lkt\Instances\LktHttpRequestLog;
use Lkt\Instances\LktUser;

Schema::add(
    Schema::table('lkt_http_requests_logs', LaminimComponent::HTTPRequestLog->value)
        ->setInstanceSettings(
            InstanceSettings::define(LktHttpRequestLog::class)
                ->setNamespaceForGeneratedClass('Lkt\Generated')
                ->setWhereStoreGeneratedClass(__DIR__ . '/../../Generated')
        )
        ->setItemsPerPage(20)
        ->setCountableField('id')
        ->setFields([
            IntegerField::identifier('id'),

            DateTimeField::define('createdAt', 'created_at')
                ->setDefaultReadFormat('Y-m-d H:i:s')
                ->setCurrentTimeStampAsDefaultValue(),

            DateTimeField::define('updatedAt', 'updated_at')
                ->setDefaultReadFormat('Y-m-d H:i:s')
                ->setCurrentTimeStampAsDefaultValue()
                ->setCurrentTimeStampOnUpdate(),

            ForeignKeyField::defineRelation(LaminimComponent::User->value, 'userId', 'user_id')->setDefaultValue([LktUser::class, 'getSignedInUserId']),

            StringField::define('route'),
            StringField::define('method'),
            IntegerField::define('responseStatus', 'response_status'),
            JSONField::associative('payload'),
            JSONField::associative('request'),

            StringField::define('clientProtocol', 'client_protocol'),
            StringField::define('clientUserAgent', 'client_useragent'),
            StringField::define('clientIPAddress', 'client_ip_address'),
            StringField::define('clientOS', 'client_os'),
            StringField::define('clientBrowser', 'client_browser'),
            StringField::define('clientBrowserVersion', 'client_browser_version'),
            MethodGetterField::define('getFormattedPayload', 'getFormattedPayload'),
        ])

        ->addAccessPolicy('app', [
            'route',
            'method',
            'responseStatus',
            'payload',
            'request',
        ])

        ->addAccessPolicy('admin', [
            'id',
            'createdAt',
            'route',
            'method',
            'responseStatus',
            'getFormattedPayload' => 'payload',
        ])
        ->addAccessPolicy('create', [
            'id',
            'route',
            'method',
            'responseStatus',
            'payload',
            'request',
            'clientProtocol',
            'clientUserAgent',
            'clientIPAddress',
            'clientOS',
            'clientBrowser',
            'clientBrowserVersion',
        ])
);