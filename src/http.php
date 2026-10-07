<?php

namespace Lkt;

use Lkt\FileBrowser\Http\FileBrowserHttp;
use Lkt\Http\BasicHttpHandler;
use Lkt\Http\Enums\ParamType;
use Lkt\Http\Routes\DeleteRoute;
use Lkt\Http\Routes\GetRoute;
use Lkt\Http\Routes\PostRoute;
use Lkt\Http\Routes\PutRoute;
use Lkt\Translations\Http\LktTranslationsHttp;
use Lkt\WebPages\Http\LktWebElementHttp;
use Lkt\WebPages\Http\LktWebPageHttp;

/**
 * Setup admin web items routes
 */
GetRoute::admin('/admin-api/ls/{component}', BasicHttpHandler::List)
    ->setName('admin-ls-web-items')
    ->setMandatoryParam('component', ParamType::String, null, ['isWebItemIdentifier' => true])
    ->setExpectedResponseDataProperty('results')
    ->setExpectedResponsePermsProperty('perm')
    ->setAllowAnonymousParams(true)
    ->setWebItemValueParamsExtractionKey('component')
    ->setRequiredPermissions(['ls'])
    ->setGrantedPermsAttempt(['mk' => 'create'])
    ->setTargetAccessPolicyAttempts('admin-ls');

GetRoute::admin('/admin-api/page-{page:\d+}/{component}', BasicHttpHandler::Page)
    ->setName('admin-pg-web-items')
    ->setMandatoryParam('component', ParamType::String, null, ['isWebItemIdentifier' => true])
    ->setMandatoryParam('page', ParamType::Number, null)
    ->setExpectedResponseDataProperty('results')
    ->setExpectedResponsePermsProperty('perm')
    ->setAllowAnonymousParams(true)
    ->setWebItemValueParamsExtractionKey('component')
    ->setPageValueParamsExtractionKey('page')
    ->setRequiredPermissions(['ls'])
    ->setGrantedPermsAttempt(['mk' => 'create'])
    ->setTargetAccessPolicyAttempts(['admin-pg', 'admin-ls']);

GetRoute::admin('/admin-api/opts-{page:\d+}/{component}', BasicHttpHandler::Page)
    ->setName('admin-opts-web-items')
    ->setMandatoryParam('component', ParamType::String, null, ['isWebItemIdentifier' => true])
    ->setMandatoryParam('page', ParamType::Number, null)
    ->setExpectedResponseDataProperty('results')
    ->setExpectedResponsePermsProperty('perm')
    ->setAllowAnonymousParams(true)
    ->setWebItemValueParamsExtractionKey('component')
    ->setPageValueParamsExtractionKey('page')
    ->setRequiredPermissions(['ls'])
    ->setTargetAccessPolicy('lkt-related')
    ->setTargetAccessPolicyAttempts(['admin-opt', 'admin-pg', 'admin-ls']);

GetRoute::admin('/admin-api/r-{id}/{component}', BasicHttpHandler::Read)
    ->setName('admin-r-web-item')
    ->setMandatoryParam('component', ParamType::String, null, ['isWebItemIdentifier' => true])
    ->setMandatoryParam('id', ParamType::Number, null)
    ->setExpectedResponseIdProperty('id')
    ->setWebItemValueParamsExtractionKey('component')
    ->setIdColumnValueParamsExtractionKey('id')
    ->setRequiredPermissions(['r'])
    ->setGrantedPermsAttempt(['up' => ['update', 'duplicate', 'switch-edit-mode'], 'rm' => 'drop'])
    ->setTargetAccessPolicy('admin');

PostRoute::admin('/admin-api/mk/{component}', BasicHttpHandler::Create)
    ->setName('admin-mk-web-item')
    ->setMandatoryParam('component', ParamType::String, null, ['isWebItemIdentifier' => true])
    ->setMandatoryParam('payload', ParamType::NotDefined, null)
    ->setWebItemValueParamsExtractionKey('component')
    ->setAnonymousTarget()
    ->setRequiredPermissions(['mk'])
    ->setPayloadValueParamsExtractionKey('payload')
    ->setTargetAccessPolicy('admin');

PutRoute::admin('/admin-api/up/{component}', BasicHttpHandler::Update)
    ->setName('admin-up-web-item')
    ->setMandatoryParam('component', ParamType::String, null, ['isWebItemIdentifier' => true])
    ->setMandatoryParam('payload', ParamType::NotDefined, null)
    ->setExpectedResponseIdProperty('id')
    ->setWebItemValueParamsExtractionKey('component')
    ->setIdColumnValueParamsExtractionKey('payload.id')
    ->setRequiredPermissions(['up'])
    ->setPayloadValueParamsExtractionKey('payload')
    ->setTargetAccessPolicy('admin');

PostRoute::admin('/admin-api/dup/{component}', BasicHttpHandler::Duplicate)
    ->setName('admin-dup-web-item')
    ->setMandatoryParam('component', ParamType::String, null, ['isWebItemIdentifier' => true])
    ->setMandatoryParam('payload', ParamType::NotDefined, null)
    ->setExpectedResponseIdProperty('id')
    ->setWebItemValueParamsExtractionKey('component')
    ->setIdColumnValueParamsExtractionKey('payload.id')
    ->setRequiredPermissions(['mk'])
    ->setPayloadValueParamsExtractionKey('payload')
    ->setTargetAccessPolicy('duplicate');

DeleteRoute::admin('/admin-api/rm/{component}', BasicHttpHandler::Drop)
    ->setName('admin-rm-web-item')
    ->setMandatoryParam('component', ParamType::String, null, ['isWebItemIdentifier' => true])
    ->setMandatoryParam('payload', ParamType::NotDefined, null)
    ->setWebItemValueParamsExtractionKey('component')
    ->setIdColumnValueParamsExtractionKey('payload.id')
    ->setPayloadValueParamsExtractionKey('payload')
    ->setRequiredPermissions(['rm'])
    ->setTargetAccessPolicy('admin');

/**
 * Setup app web items routes
 */
GetRoute::onlyLoggedUsers('/api/ls/{component}-{accessPolicy}', BasicHttpHandler::List)
    ->setName('ls-web-items-policy')
    ->setMandatoryParam('component', ParamType::String, null, ['isWebItemIdentifier' => true])
    ->setMandatoryParam('accessPolicy', ParamType::String, null)
    ->setExpectedResponseDataProperty('results')
    ->setExpectedResponsePermsProperty('perm')
    ->setAllowAnonymousParams(true)
    ->setWebItemValueParamsExtractionKey('component')
    ->setTargetAccessPolicyExtractionKey('accessPolicy')
    ->setRequiredPermissions(['ls'])
    ->setGrantedPermsAttempt(['mk' => 'create'])
    ->setTargetAccessPolicyAttempts('ls');

GetRoute::onlyLoggedUsers('/api/ls/{component}', BasicHttpHandler::List)
    ->setName('ls-web-items')
    ->setMandatoryParam('component', ParamType::String, null, ['isWebItemIdentifier' => true])
    ->setExpectedResponseDataProperty('results')
    ->setExpectedResponsePermsProperty('perm')
    ->setAllowAnonymousParams(true)
    ->setWebItemValueParamsExtractionKey('component')
    ->setRequiredPermissions(['ls'])
    ->setGrantedPermsAttempt(['mk' => 'create'])
    ->setTargetAccessPolicyAttempts('ls');

GetRoute::onlyLoggedUsers('/api/page-{page:\d+}-{accessPolicy}/{component}', BasicHttpHandler::Page)
    ->setName('pg-web-items-policy')
    ->setMandatoryParam('component', ParamType::String, null, ['isWebItemIdentifier' => true])
    ->setMandatoryParam('page', ParamType::Number, null)
    ->setMandatoryParam('accessPolicy', ParamType::String, null)
    ->setExpectedResponseDataProperty('results')
    ->setExpectedResponsePermsProperty('perm')
    ->setAllowAnonymousParams(true)
    ->setWebItemValueParamsExtractionKey('component')
    ->setPageValueParamsExtractionKey('page')
    ->setRequiredPermissions(['ls'])
    ->setGrantedPermsAttempt(['mk' => 'create'])
    ->setTargetAccessPolicyAttempts(['pg', 'ls']);

GetRoute::onlyLoggedUsers('/api/page-{page:\d+}/{component}', BasicHttpHandler::Page)
    ->setName('pg-web-items')
    ->setMandatoryParam('component', ParamType::String, null, ['isWebItemIdentifier' => true])
    ->setMandatoryParam('page', ParamType::Number, null)
    ->setExpectedResponseDataProperty('results')
    ->setExpectedResponsePermsProperty('perm')
    ->setAllowAnonymousParams(true)
    ->setWebItemValueParamsExtractionKey('component')
    ->setPageValueParamsExtractionKey('page')
    ->setTargetAccessPolicyExtractionKey('accessPolicy')
    ->setRequiredPermissions(['ls'])
    ->setGrantedPermsAttempt(['mk' => 'create'])
    ->setTargetAccessPolicyAttempts(['pg', 'ls']);

GetRoute::onlyLoggedUsers('/api/opts-{page:\d+}-{accessPolicy}/{component}', BasicHttpHandler::Page)
    ->setName('opts-web-items-policy')
    ->setMandatoryParam('component', ParamType::String, null, ['isWebItemIdentifier' => true])
    ->setMandatoryParam('page', ParamType::Number, null)
    ->setMandatoryParam('accessPolicy', ParamType::String, null)
    ->setExpectedResponseDataProperty('results')
    ->setExpectedResponsePermsProperty('perm')
    ->setAllowAnonymousParams(true)
    ->setWebItemValueParamsExtractionKey('component')
    ->setPageValueParamsExtractionKey('page')
    ->setTargetAccessPolicyExtractionKey('accessPolicy')
    ->setRequiredPermissions(['ls'])
    ->setTargetAccessPolicy('lkt-related')
    ->setTargetAccessPolicyAttempts(['opt', 'pg', 'ls']);

GetRoute::onlyLoggedUsers('/api/opts-{page:\d+}/{component}', BasicHttpHandler::Page)
    ->setName('opts-web-items')
    ->setMandatoryParam('component', ParamType::String, null, ['isWebItemIdentifier' => true])
    ->setMandatoryParam('page', ParamType::Number, null)
    ->setExpectedResponseDataProperty('results')
    ->setExpectedResponsePermsProperty('perm')
    ->setAllowAnonymousParams(true)
    ->setWebItemValueParamsExtractionKey('component')
    ->setPageValueParamsExtractionKey('page')
    ->setRequiredPermissions(['ls'])
    ->setTargetAccessPolicy('lkt-related')
    ->setTargetAccessPolicyAttempts(['opt', 'pg', 'ls']);

GetRoute::onlyLoggedUsers('/api/r-{id}-{accessPolicy}/{component}', BasicHttpHandler::Read)
    ->setName('admin-r-web-item-policy')
    ->setMandatoryParam('component', ParamType::String, null, ['isWebItemIdentifier' => true])
    ->setMandatoryParam('id', ParamType::Number, null)
    ->setMandatoryParam('accessPolicy', ParamType::String, null)
    ->setExpectedResponseIdProperty('id')
    ->setWebItemValueParamsExtractionKey('component')
    ->setIdColumnValueParamsExtractionKey('id')
    ->setTargetAccessPolicyExtractionKey('accessPolicy')
    ->setRequiredPermissions(['r'])
    ->setGrantedPermsAttempt(['up' => ['update', 'duplicate', 'switch-edit-mode'], 'rm' => 'drop'])
    ->setTargetAccessPolicy('app');

GetRoute::onlyLoggedUsers('/api/r-{id}/{component}', BasicHttpHandler::Read)
    ->setName('admin-r-web-item')
    ->setMandatoryParam('component', ParamType::String, null, ['isWebItemIdentifier' => true])
    ->setMandatoryParam('id', ParamType::Number, null)
    ->setExpectedResponseIdProperty('id')
    ->setWebItemValueParamsExtractionKey('component')
    ->setIdColumnValueParamsExtractionKey('id')
    ->setRequiredPermissions(['r'])
    ->setGrantedPermsAttempt(['up' => ['update', 'duplicate', 'switch-edit-mode'], 'rm' => 'drop'])
    ->setTargetAccessPolicy('app');

PostRoute::onlyLoggedUsers('/api/mk/{component}', BasicHttpHandler::Create)
    ->setName('mk-web-item')
    ->setMandatoryParam('component', ParamType::String, null, ['isWebItemIdentifier' => true])
    ->setMandatoryParam('payload', ParamType::NotDefined, null)
    ->setWebItemValueParamsExtractionKey('component')
    ->setAnonymousTarget()
    ->setRequiredPermissions(['mk'])
    ->setPayloadValueParamsExtractionKey('payload')
    ->setTargetAccessPolicy('app');

PutRoute::onlyLoggedUsers('/api/up/{component}', BasicHttpHandler::Update)
    ->setName('up-web-item')
    ->setMandatoryParam('component', ParamType::String, null, ['isWebItemIdentifier' => true])
    ->setMandatoryParam('payload', ParamType::NotDefined, null)
    ->setExpectedResponseIdProperty('id')
    ->setWebItemValueParamsExtractionKey('component')
    ->setIdColumnValueParamsExtractionKey('payload.id')
    ->setRequiredPermissions(['up'])
    ->setPayloadValueParamsExtractionKey('payload')
    ->setTargetAccessPolicy('app');

PostRoute::onlyLoggedUsers('/api/dup/{component}', BasicHttpHandler::Duplicate)
    ->setName('dup-web-item')
    ->setMandatoryParam('component', ParamType::String, null, ['isWebItemIdentifier' => true])
    ->setMandatoryParam('payload', ParamType::NotDefined, null)
    ->setExpectedResponseIdProperty('id')
    ->setWebItemValueParamsExtractionKey('component')
    ->setIdColumnValueParamsExtractionKey('payload.id')
    ->setRequiredPermissions(['mk'])
    ->setPayloadValueParamsExtractionKey('payload')
    ->setTargetAccessPolicy('duplicate');

DeleteRoute::onlyLoggedUsers('/api/rm/{component}', BasicHttpHandler::Drop)
    ->setName('rm-web-item')
    ->setMandatoryParam('component', ParamType::String, null, ['isWebItemIdentifier' => true])
    ->setMandatoryParam('payload', ParamType::NotDefined, null)
    ->setWebItemValueParamsExtractionKey('component')
    ->setIdColumnValueParamsExtractionKey('payload.id')
    ->setPayloadValueParamsExtractionKey('payload')
    ->setRequiredPermissions(['rm'])
    ->setTargetAccessPolicy('app');

/**
 * Public translations routes
 */
GetRoute::register('/i18n', [LktTranslationsHttp::class, 'i18n']);

/**
 * Translations admin routes
 */
GetRoute::admin('/translations', [LktTranslationsHttp::class, 'index']);
GetRoute::admin('/translations/export', [LktTranslationsHttp::class, 'export']);
GetRoute::admin('/translations/export/missing', [LktTranslationsHttp::class, 'exportMissing']);
PostRoute::admin('/translation', [LktTranslationsHttp::class, 'create']);
GetRoute::admin('/translation/{id}', [LktTranslationsHttp::class, 'read']);
PutRoute::admin('/translation/{id}', [LktTranslationsHttp::class, 'update']);
DeleteRoute::admin('/translation/{id}', [LktTranslationsHttp::class, 'drop']);

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
GetRoute::admin('/web/pages', [LktWebPageHttp::class, 'index']);
GetRoute::admin('/web/pages/{type}', [LktWebPageHttp::class, 'index']);

PostRoute::admin('/web/page', [LktWebPageHttp::class, 'create']);
GetRoute::admin('/web/page/{id:\d+}', [LktWebPageHttp::class, 'read']);
GetRoute::admin('/web/page/{id:\d+}/children', [LktWebPageHttp::class, 'children']);
PutRoute::admin('/web/page/{id:\d+}', [LktWebPageHttp::class, 'update']);
DeleteRoute::admin('/web/page/{id:\d+}', [LktWebPageHttp::class, 'drop']);
GetRoute::register('/web/page', [LktWebPageHttp::class, 'view']);

/**
 * File browser
 */
GetRoute::register('/file-browser', [FileBrowserHttp::class, 'fileBrowser']);
PostRoute::register('/file-browser/entity', [FileBrowserHttp::class, 'createFileEntity']);
PutRoute::register('/file-browser/entity/{id}', [FileBrowserHttp::class, 'updateFileEntity']);
GetRoute::register('/file-browser/entity/{id}', [FileBrowserHttp::class, 'readFileEntity']);
GetRoute::register('/file-browser/entity/file/{id}', [FileBrowserHttp::class, 'openFile']);