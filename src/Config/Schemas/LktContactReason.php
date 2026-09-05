<?php

namespace Lkt\Config\Schemas;

use Lkt\Enums\LaminimComponent;
use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\ForeignKeyField;
use Lkt\Factory\Schemas\Fields\IntegerField;
use Lkt\Factory\Schemas\Fields\JSONField;
use Lkt\Factory\Schemas\Fields\StringField;
use Lkt\Factory\Schemas\InstanceSettings;
use Lkt\Factory\Schemas\PrefabFields\VisibilityStatusField;
use Lkt\Factory\Schemas\Schema;
use Lkt\Instances\LktContactReason;
use Lkt\Instances\LktUser;
use Lkt\WebPages\Enums\WebPageStatus;

Schema::add(
    Schema::table('lkt_contact_reasons', LaminimComponent::ContactReason->value)
        ->setInstanceSettings(InstanceSettings::simple(LktContactReason::class, 'Lkt\Generated', __DIR__ . '/../../Generated')
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

            StringField::define('name')->setIsI18nJson()->setIsUnique(),
            JSONField::associativeI18n('nameData', 'name'),
            VisibilityStatusField::define()->setDefaultValue(WebPageStatus::Public->value)
        ])

        ->setItemToI18nPolicy('contactReasons', 'id', 'name')

        ->addAccessPolicy('admin', [
            'id',
            'name',
            'status',
        ])
);