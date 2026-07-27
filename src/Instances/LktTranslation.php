<?php

namespace Lkt\Instances;

use Lkt\Generated\GeneratedLktTranslation;
use Lkt\Translations\Enums\TranslationType;

class LktTranslation extends GeneratedLktTranslation
{
    const COMPONENT = 'lkt-i18n';

    public static function createOrUpdate(string $property, TranslationType $type, array $value = [], int $parentId = 0): static
    {
        $query = static::getQueryCaller()->andPropertyEqual($property);
        $query->andParentEqual($parentId)->setForceRefresh(true);
        $instance = static::getOne($query);
        $payload = [
            'type' => $type->value,
            'property' => $property,
            'valueData' => $value,
            'parentId' => $parentId,
        ];
        if (!$instance) {
            $instance = LktTranslation::getInstance()->feedAndSave($payload);
        } else {
            $instance->feedAndSave($payload);
        }
        return $instance;
    }

    public static function createIfMissing(string $property, TranslationType $type, array $value = [], int $parentId = 0): static
    {
        $query = static::getQueryCaller()->andPropertyEqual($property);
        $query->andParentEqual($parentId)->setForceRefresh(true);
        $instance = static::getOne($query);
        if (!$instance) {
            $instance = LktTranslation::getInstance()->feedAndSave([
                'type' => $type->value,
                'property' => $property,
                'valueData' => $value,
                'parentId' => $parentId,
            ]);
        }
        return $instance;
    }
}