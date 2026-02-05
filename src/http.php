<?php
namespace Lkt;

use Lkt\FileBrowser\Http\FileBrowserHttp;
use Lkt\Http\BasicHttpHandler;
use Lkt\Http\Routes\DeleteRoute;
use Lkt\Http\Routes\GetRoute;
use Lkt\Http\Routes\PostRoute;
use Lkt\Http\Routes\PutRoute;
use Lkt\WebPages\Http\LktWebElementHttp;
use Lkt\WebPages\Http\LktWebPageHttp;

/**
 * Setup admin web items routes
 */
GetRoute::admin('/admin-api/ls/{component}', BasicHttpHandler::List)
    ->setWebItemValueParamsExtractionKey('component')
    ->setRequiredPermissions(['ls'])
    ->setGrantedPermsAttempt(['mk' => 'create'])
;

GetRoute::admin('/admin-api/page-{page:\d+}/{component}', BasicHttpHandler::Page)
    ->setWebItemValueParamsExtractionKey('component')
    ->setPageValueParamsExtractionKey('page')
    ->setRequiredPermissions(['ls'])
    ->setGrantedPermsAttempt(['mk' => 'create'])
;

GetRoute::admin('/admin-api/r-{id:\d+}/{component}', BasicHttpHandler::Read)
    ->setWebItemValueParamsExtractionKey('component')
    ->setIdColumnValueParamsExtractionKey('id')
    ->setRequiredPermissions(['r'])
    ->setGrantedPermsAttempt(['up' => ['update', 'switch-edit-mode'], 'rm' => 'drop'])
    ->setTargetAccessPolicy('admin')
;

PostRoute::admin('/admin-api/mk/{component}', BasicHttpHandler::Create)
    ->setWebItemValueParamsExtractionKey('component')
    ->setAnonymousTarget()
    ->setRequiredPermissions(['mk'])
    ->setPayloadValueParamsExtractionKey('payload')
    ->setTargetAccessPolicy('admin')
;

PutRoute::admin('/admin-api/up/{component}', BasicHttpHandler::Update)
    ->setWebItemValueParamsExtractionKey('component')
    ->setIdColumnValueParamsExtractionKey('payload.id')
    ->setRequiredPermissions(['up'])
    ->setPayloadValueParamsExtractionKey('payload')
    ->setTargetAccessPolicy('admin')
;

DeleteRoute::admin('/admin-api/rm/{component}', BasicHttpHandler::Drop)
    ->setWebItemValueParamsExtractionKey('component')
    ->setIdColumnValueParamsExtractionKey('payload.id')
    ->setRequiredPermissions(['rm'])
    ->setTargetAccessPolicy('admin')
;

/**
 * Web Elements Routes
 */
PostRoute::register('/web/element', [LktWebElementHttp::class, 'create']);
GetRoute::register('/web/element/{id}', [LktWebElementHttp::class, 'read']);
GetRoute::register('/web/element/{id}/children', [LktWebElementHttp::class, 'children']);
PutRoute::register('/web/element/{id}', [LktWebElementHttp::class, 'update']);
DeleteRoute::register('/web/element/{id}', [LktWebElementHttp::class, 'drop']);

/**
 * Web Pages Routes
 */
GetRoute::register('/web/pages', [LktWebPageHttp::class, 'index']);
GetRoute::register('/web/pages/{type}', [LktWebPageHttp::class, 'index']);

PostRoute::register('/web/page', [LktWebPageHttp::class, 'create']);
GetRoute::register('/web/page', [LktWebPageHttp::class, 'view']);
GetRoute::register('/web/page/{id:\d+}', [LktWebPageHttp::class, 'read']);
GetRoute::register('/web/page/{id:\d+}/children', [LktWebPageHttp::class, 'children']);
PutRoute::register('/web/page/{id:\d+}', [LktWebPageHttp::class, 'update']);
DeleteRoute::register('/web/page/{id:\d+}', [LktWebPageHttp::class, 'drop']);

/**
 * File browser
 */
GetRoute::register('/file-browser', [FileBrowserHttp::class, 'fileBrowser']);
PostRoute::register('/file-browser/entity', [FileBrowserHttp::class, 'createFileEntity']);
PutRoute::register('/file-browser/entity/{id}', [FileBrowserHttp::class, 'updateFileEntity']);
GetRoute::register('/file-browser/entity/file/{id}', [FileBrowserHttp::class, 'openFile']);