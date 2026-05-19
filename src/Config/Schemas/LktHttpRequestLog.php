<?php

namespace Lkt\Config\Schemas;

use Lkt\Factory\Schemas\Fields\AssocJSONField;
use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\ForeignKeyField;
use Lkt\Factory\Schemas\Fields\IdField;
use Lkt\Factory\Schemas\Fields\IntegerField;
use Lkt\Factory\Schemas\Fields\MethodGetterField;
use Lkt\Factory\Schemas\Fields\StringField;
use Lkt\Factory\Schemas\InstanceSettings;
use Lkt\Factory\Schemas\Schema;
use Lkt\Instances\LktHttpRequestLog;
use Lkt\Instances\LktUser;

Schema::add(
    Schema::table('lkt_http_requests_logs', LktHttpRequestLog::COMPONENT)
        ->setInstanceSettings(
            InstanceSettings::define(LktHttpRequestLog::class)
                ->setNamespaceForGeneratedClass('Lkt\Generated')
                ->setWhereStoreGeneratedClass(__DIR__ . '/../../Generated')
        )
        ->setItemsPerPage(20)
        ->setCountableField('id')
        ->addField(IdField::define('id'))
        ->addField(
            DateTimeField::define('createdAt', 'created_at')
                ->setDefaultReadFormat('Y-m-d H:i:s')
                ->setCurrentTimeStampAsDefaultValue()
        )
        ->addField(
            DateTimeField::define('updatedAt', 'updated_at')
                ->setDefaultReadFormat('Y-m-d H:i:s')
                ->setCurrentTimeStampAsDefaultValue()
                ->setCurrentTimeStampOnUpdate()
        )
        ->addField(ForeignKeyField::defineRelation(LktUser::COMPONENT, 'userId', 'user_id')->setDefaultValue([LktUser::class, 'getSignedInUserId']))

        ->addField(StringField::define('route'))
        ->addField(StringField::define('method'))
        ->addField(IntegerField::define('responseStatus', 'response_status'))
        ->addField(AssocJSONField::define('payload'))
        ->addField(AssocJSONField::define('request'))
        ->addField(StringField::define('clientProtocol', 'client_protocol'))
        ->addField(StringField::define('clientUserAgent', 'client_useragent'))
        ->addField(StringField::define('clientIPAddress', 'client_ip_address'))
        ->addField(StringField::define('clientOS', 'client_os'))
        ->addField(StringField::define('clientBrowser', 'client_browser'))
        ->addField(StringField::define('clientBrowserVersion', 'client_browser_version'))
        ->addField(MethodGetterField::define('getFormattedPayload', 'getFormattedPayload'))

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