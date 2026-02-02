<?php

namespace Lkt\Config\Schemas;

use Lkt\Factory\Schemas\Fields\AssocJSONField;
use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\ForeignKeysField;
use Lkt\Factory\Schemas\Fields\IdField;
use Lkt\Factory\Schemas\Fields\IntegerField;
use Lkt\Factory\Schemas\Fields\StringField;
use Lkt\Factory\Schemas\InstanceSettings;
use Lkt\Factory\Schemas\Schema;
use Lkt\Instances\LktWebElement;

Schema::add(
    Schema::table('lkt_web_elements', LktWebElement::COMPONENT)
        ->setInstanceSettings(
            InstanceSettings::define(LktWebElement::class)
                ->setNamespaceForGeneratedClass('Lkt\Generated')
                ->setWhereStoreGeneratedClass(__DIR__ . '/../../Generated')
        )
        ->setItemsPerPage(20)
        ->setCountableField('id')
        ->setRelatedAccessPolicy([
            'id' => 'value',
            'component' => 'label',
            'id',
            'component',
            'type',
            'props',
            'config',
            'layout',
            'children',
            'subElements',
        ])
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
        ->addField(IntegerField::define('type'))
        ->addField(StringField::define('component'))
        ->addField(AssocJSONField::define('props'))
        ->addField(AssocJSONField::define('config'))
        ->addField(AssocJSONField::define('layout'))
        ->addField(AssocJSONField::define('subElements', 'sub_elements'))
        ->addField(
            ForeignKeysField::defineRelation(LktWebElement::COMPONENT, 'children')
        )
);