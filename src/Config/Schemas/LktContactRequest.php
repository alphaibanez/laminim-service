<?php

namespace Lkt\Config\Schemas;

use Lkt\Enums\LaminimComponent;
use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\EmailField;
use Lkt\Factory\Schemas\Fields\ForeignKeyField;
use Lkt\Factory\Schemas\Fields\IntegerField;
use Lkt\Factory\Schemas\Fields\StringField;
use Lkt\Factory\Schemas\InstanceSettings;
use Lkt\Factory\Schemas\Schema;
use Lkt\Instances\LktContactRequest;
use Lkt\Instances\LktUser;

Schema::add(
    Schema::table('lkt_contact_requests', LaminimComponent::ContactRequest->value)
        ->setInstanceSettings(InstanceSettings::simple(LktContactRequest::class, 'Lkt\Generated', __DIR__ . '/../../Generated')
            ->setAbstractInstanceExtends(false)
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

            ForeignKeyField::defineRelation(LaminimComponent::User->value, 'createdBy', 'created_by')->setDefaultValue([LktUser::class, 'getSignedInUserId']),

            StringField::define('name'),
            EmailField::define('email'),
            StringField::define('phone'),
            StringField::define('message'),
            ForeignKeyField::defineRelation(LaminimComponent::ContactReason->value, 'contactReason', 'contact_reason_id'),
            StringField::define('clientProtocol', 'client_protocol'),
            StringField::define('clientUserAgent', 'client_useragent'),
            StringField::define('clientIPAddress', 'client_ip_address'),
            StringField::define('clientOS', 'client_os'),
            StringField::define('clientBrowser', 'client_browser'),
            StringField::define('clientBrowserVersion', 'client_browser_version'),
        ])

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