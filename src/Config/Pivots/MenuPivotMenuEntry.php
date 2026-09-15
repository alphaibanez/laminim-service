<?php

namespace Lkt\Config\Pivots;

use Lkt\Enums\LaminimComponent;
use Lkt\Factory\Schemas\Fields\PivotLeftIdField;
use Lkt\Factory\Schemas\Fields\PivotPositionField;
use Lkt\Factory\Schemas\Fields\PivotRightIdField;
use Lkt\Factory\Schemas\InstanceSettings;
use Lkt\Factory\Schemas\Schema;
use Lkt\Instances\LktMenuPivotEntry;

Schema::add(
    Schema::pivotTable('lkt_menus__entries', LaminimComponent::MenuPivotEntry->value)
        ->setInstanceSettings(InstanceSettings::simple(LktMenuPivotEntry::class, 'Lkt\Generated', __DIR__ . '/../../Generated'))
        ->setFields([
            PivotLeftIdField::defineRelation(LaminimComponent::Menu->value, 'menu', 'menu_id'),
            PivotRightIdField::defineRelation(LaminimComponent::MenuEntry->value, 'entry', 'entry_id'),
            PivotPositionField::define('position'),
        ])
);