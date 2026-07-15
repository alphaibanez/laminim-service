<?php

namespace Lkt\FileBrowser\Http;

use Lkt\FileBrowser\Enums\FileEntityType;
use Lkt\Http\Response;
use Lkt\Instances\LktFileEntity;

class FileBrowserHttp
{
    public static function fileBrowser(array $params): Response
    {
        $query = LktFileEntity::getQueryCaller()->andTypeEqual(FileEntityType::StorageUnit->value);

        $units = LktFileEntity::getMany($query);
        if (count($units) === 0) {
            $unit = LktFileEntity::getInstance()
                ->autoCreate([
                    'nameData' => ['en' => 'Root', 'es' => 'Raíz'],
                    'type' => FileEntityType::StorageUnit->value,
                ]);

            $units[] = $unit;
        }

        return Response::ok(array_map(function (LktFileEntity $entity) { return $entity->autoRead(); }, $units));
    }

    public static function createFileEntity(array $params): Response
    {
        $entity = LktFileEntity::getInstance();
        $entity->doCreate($params);

        return Response::ok();
    }

    public static function readFileEntity(array $params): Response
    {
        $entity = LktFileEntity::getInstance((int)$params['id']);

        return Response::ok([
            'item' => $entity->autoRead(),
        ]);
    }

    public static function updateFileEntity(array $params): Response
    {
        $entity = LktFileEntity::getInstance((int)$params['id']);
        $entity->doUpdate($params);

        return Response::ok();
    }

    public static function openFile(array $params): Response
    {
        $entity = LktFileEntity::getInstance((int)$params['id']);
        return $entity->getSrcResponse();
    }
}