<?php

namespace Lkt\Config\Schemas;

use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\EmailField;
use Lkt\Factory\Schemas\Fields\ForeignKeyField;
use Lkt\Factory\Schemas\Fields\IdField;
use Lkt\Factory\Schemas\Fields\StringField;
use Lkt\Factory\Schemas\InstanceSettings;
use Lkt\Factory\Schemas\Schema;
use Lkt\Instances\LktContactReason;
use Lkt\Instances\LktContactRequest;
use Lkt\Instances\LktUser;

Schema::add(
    Schema::table('lkt_contact_requests', LktContactRequest::COMPONENT)
        ->setInstanceSettings(
            InstanceSettings::define(LktContactRequest::class)
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
        ->addField(ForeignKeyField::defineRelation(LktUser::COMPONENT, 'createdBy', 'created_by')->setDefaultValue([LktUser::class, 'getSignedInUserId']))
        ->addField(StringField::define('name'))
        ->addField(EmailField::define('email'))
        ->addField(StringField::define('phone'))
        ->addField(StringField::define('message'))
        ->addField(ForeignKeyField::defineRelation(LktContactReason::COMPONENT, 'contactReason', 'contact_reason_id'))
        ->addField(StringField::define('clientProtocol', 'client_protocol'))
        ->addField(StringField::define('clientUserAgent', 'client_useragent'))
        ->addField(StringField::define('clientIPAddress', 'client_ip_address'))
        ->addField(StringField::define('clientOS', 'client_os'))
        ->addField(StringField::define('clientBrowser', 'client_browser'))
        ->addField(StringField::define('clientBrowserVersion', 'client_browser_version'))

        ->addAccessPolicy('app', [
            'name',
            'message',
            'email',
            'phone',
            'contactReason',
        ])

        ->addAccessPolicy('admin', [
            'name',
            'message',
            'email',
            'phone',
            'contactReason',
        ])
        ->addAccessPolicy('create', [
            'id',
            'name',
            'message',
            'email',
            'phone',
            'contactReason',
            'clientProtocol',
            'clientUserAgent',
            'clientIPAddress',
            'clientOS',
            'clientBrowser',
            'clientBrowserVersion',
        ])
);