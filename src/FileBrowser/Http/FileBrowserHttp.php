<?php

namespace Lkt\FileBrowser\Http;

use Lkt\Instances\LktFileEntity;
use Lkt\Http\Response;

class FileBrowserHttp
{
    public static function fileBrowser(array $params): Response
    {
        $unit = LktFileEntity::getInstance(1);

        return Response::ok([$unit->autoRead()]);
    }

    public static function createFileEntity(array $params): Response
    {
        $entity = LktFileEntity::getInstance();
        $entity->doCreate($params);

        return Response::ok();
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