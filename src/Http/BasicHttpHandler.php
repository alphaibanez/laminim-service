<?php

namespace Lkt\Http;

use Lkt\Connectors\Cache\QueryCache;
use Lkt\Controllers\LktPermissionController;
use Lkt\Debug\VarDumper;
use Lkt\Enums\Permission;
use Lkt\Factory\Schemas\Enums\AccessPolicyEndOfLife;
use Lkt\Factory\Schemas\Schema;
use Lkt\Http\Enums\AccessLevel;
use Lkt\Http\Enums\HttpEvent;
use Lkt\Users\Enums\RoleCapability;
use Lkt\WebItems\Enums\WebItemAction;
use Lkt\WebItems\Enums\WebItemActionHook;

class BasicHttpHandler
{
    public const Page = [self::class, 'pg'];
    public const List = [self::class, 'ls'];
    public const Create = [self::class, 'mk'];
    public const Read = [self::class, 'r'];
    public const Update = [self::class, 'up'];
    public const Duplicate = [self::class, 'dup'];
    public const Drop = [self::class, 'rm'];

    public static function r(Request $request): Response
    {
        $accessPolicy = $request->getTargetAccessPolicy(WebItemAction::Read);
        if ($accessPolicy instanceof Response) return $accessPolicy;

        if ($accessPolicy) {
            $request->targetInstance->setAccessPolicy($accessPolicy, AccessPolicyEndOfLife::UntilNextRead);
        }

        $perm = [];
        if ($request->loggedUser) {
            $perm = $request->loggedUser->attemptToGrantPermissions(
                $request->accessLevel,
                $request->targetComponent,
                $request->attemptToGrantPerms,
                $request->targetInstance,
            );
        }

        $perm = array_unique($perm);

        $r = [
            'item' => $request->targetInstance->autoRead(),
            'perm' => $perm,
        ];

        if ($request->targetWebItem) {
            $r['component'] = $request->targetWebItem->publicComponentName;
        }

        $schema = Schema::get($request->targetComponent);
        $hookHandlerResponse = $schema->runWebItemActionHookHandlers(WebItemAction::Read, WebItemActionHook::TweakResponseData, [
            'data' => &$r,
            'request' => $request,
        ]);
        if ($hookHandlerResponse) return $hookHandlerResponse;

        return Response::ok($r);
    }

    public static function mk(Request $request): Response
    {
        $accessPolicy = $request->getTargetAccessPolicy(WebItemAction::Create);
        if ($accessPolicy instanceof Response) return $accessPolicy;

        if ($accessPolicy) {
            $request->targetInstance->setAccessPolicy($accessPolicy, AccessPolicyEndOfLife::UntilNextWrite);
        }

        if ($request->payload && count($request->payload) > 0) {
            $request->targetInstance->autoCreate($request->payload);

        } else {
            $request->targetInstance->autoCreate($request->params);
        }

        if ($request->httpEventHandlers) HttpEventHandler::triggerEvent(HttpEvent::SuccessCreate, $request->httpEventHandlers, []);

        $schema = Schema::get($request->targetComponent);
        $hookHandlerResponse = $schema->runWebItemActionHookHandlers(WebItemAction::Create, WebItemActionHook::Success, [
            'request' => $request
        ]);
        if ($hookHandlerResponse) return $hookHandlerResponse;

        $responseData = ['id' => $request->targetInstance->getId()];
        $hookHandlerResponse = $schema->runWebItemActionHookHandlers(WebItemAction::Create, WebItemActionHook::TweakResponseData, [
            'data' => &$responseData,
            'request' => $request,
        ]);
        if ($hookHandlerResponse) return $hookHandlerResponse;

        return Response::created($responseData);
    }

    public static function up(Request $request): Response
    {
        $accessPolicy = $request->getTargetAccessPolicy(WebItemAction::Update);
        if ($accessPolicy instanceof Response) return $accessPolicy;

        $schema = Schema::get($request->targetComponent);
        $hookHandlerResponse = $schema->runWebItemActionHookHandlers(WebItemAction::Update, WebItemActionHook::BeforeAction, [
            'request' => $request
        ]);
        if ($hookHandlerResponse) return $hookHandlerResponse;

        if ($accessPolicy) {
            $request->targetInstance->setAccessPolicy($accessPolicy, AccessPolicyEndOfLife::UntilNextWrite);
        }
        if ($request->payload && count($request->payload) > 0) {
            $request->targetInstance->autoUpdate($request->payload);

        } else {
            $request->targetInstance->autoUpdate($request->params);
        }

        if ($request->httpEventHandlers) HttpEventHandler::triggerEvent(HttpEvent::SuccessUpdate, $request->httpEventHandlers, []);

        $hookHandlerResponse = $schema->runWebItemActionHookHandlers(WebItemAction::Update, WebItemActionHook::Success, [
            'request' => $request
        ]);
        if ($hookHandlerResponse) return $hookHandlerResponse;

        $responseData = ['id' => $request->targetInstance->getId()];
        $hookHandlerResponse = $schema->runWebItemActionHookHandlers(WebItemAction::Update, WebItemActionHook::TweakResponseData, [
            'data' => &$responseData,
            'request' => $request,
        ]);
        if ($hookHandlerResponse) return $hookHandlerResponse;

        return Response::ok($responseData);
    }

    public static function dup(Request $request): Response
    {
        $accessPolicy = $request->getTargetAccessPolicy(WebItemAction::Duplicate);
        if ($accessPolicy instanceof Response) return $accessPolicy;

        if ($accessPolicy) {
            $request->targetInstance->setAccessPolicy($accessPolicy, AccessPolicyEndOfLife::UntilNextRead);
        }

        $duplicated = $request->targetInstance->saveDuplicate();

        if ($request->httpEventHandlers) HttpEventHandler::triggerEvent(HttpEvent::SuccessUpdate, $request->httpEventHandlers, []);

        $schema = Schema::get($request->targetComponent);
        $hookHandlerResponse = $schema->runWebItemActionHookHandlers(WebItemAction::Duplicate, WebItemActionHook::Success, [
            'request' => $request
        ]);
        if ($hookHandlerResponse) return $hookHandlerResponse;


        if ($request->accessLevel === AccessLevel::OnlyAdminUsers) {
            if (count(Notification::$defaultSuccessDuplicateNotificationPayload) > 0) {
                Notification::sendSuccessToast(Notification::$defaultSuccessDuplicateNotificationPayload);
            }
        }

        $responseData = ['id' => $duplicated->getId()];
        $hookHandlerResponse = $schema->runWebItemActionHookHandlers(WebItemAction::Duplicate, WebItemActionHook::TweakResponseData, [
            'data' => &$responseData,
            'request' => $request,
        ]);
        if ($hookHandlerResponse) return $hookHandlerResponse;

        return Response::ok($responseData);
    }

    public static function rm(Request $request): Response
    {
        if ($request->targetWebItem) {
            if ($request->accessLevel === AccessLevel::OnlyAdminUsers) {
                if (!in_array(WebItemAction::Drop, $request->targetWebItem->getEnabledAdminActions())) {
                    if ($request->httpEventHandlers) HttpEventHandler::triggerEvent(HttpEvent::NotEnoughPerms, $request->httpEventHandlers, []);
                    return Response::badRequest();
                }
            }
            else {
                if (!in_array(WebItemAction::Drop, $request->targetWebItem->getEnabledAppActions())) {
                    if ($request->httpEventHandlers) HttpEventHandler::triggerEvent(HttpEvent::NotEnoughPerms, $request->httpEventHandlers, []);
                    return Response::badRequest();
                }
            }
        }

        $request->targetInstance->delete();

        if ($request->httpEventHandlers) HttpEventHandler::triggerEvent(HttpEvent::SuccessDrop, $request->httpEventHandlers, []);

        $schema = Schema::get($request->targetComponent);
        $hookHandlerResponse = $schema->runWebItemActionHookHandlers(WebItemAction::Drop, WebItemActionHook::Success, [
            'request' => $request
        ]);
        if ($hookHandlerResponse) return $hookHandlerResponse;
        return Response::ok();
    }

    public static function pg(Request $request): Response
    {
        $accessPolicy = $request->getTargetAccessPolicy(WebItemAction::Page);
        if ($accessPolicy instanceof Response) return $accessPolicy;

        if (!$request->targetComponent) return Response::badRequest();

        if ($request->accessLevel === AccessLevel::OnlyAdminUsers) {
            if (!$request->loggedUser) return Response::forbidden();
            $capability = $request->loggedUser->getAdminCapability($request->targetComponent, Permission::List->value);

        } else {
            $capability = $request->loggedUser
                ? $request->loggedUser?->getAppCapability($request->targetComponent, Permission::List->value)
                : LktPermissionController::getEnsuredPublicPermission($request->targetComponent, Permission::List->value);
        }

        $schema = Schema::get($request->targetComponent);
        $helperInstance = $schema->getItemInstance();
        $builder = $helperInstance::getQueryCaller();

        if ($capability && $capability === RoleCapability::Owned) {
            $ownershipField = $schema->getOwnershipField();
            if ($ownershipField) {
                $builder->andIntegerEqual($ownershipField->getColumn(), $request->loggedUser->getId());
            }
        }

        $schema->filterBuilder($builder, $request->params);

        //@todo: check if is anonymous, get access level field from schema and filter not allowed results from query
        $hookHandlerResponse = $schema->runWebItemActionHookHandlers(WebItemAction::Page, WebItemActionHook::PrepareQueryBuilder, [
            'query' => $builder,
            'request' => $request,
        ]);
        if ($hookHandlerResponse) return $hookHandlerResponse;

        // Custom route hooks
        $customRouteHooks = $request->route->getWebItemActionHookHandlers(WebItemAction::Page, WebItemActionHook::PrepareQueryBuilder);
        $hookHandlerResponse = $schema->runWebItemActionHookHandlers(WebItemAction::Page, WebItemActionHook::PrepareQueryBuilder, [
            'query' => $builder,
            'request' => $request,
        ], $customRouteHooks);

        if ($hookHandlerResponse) return $hookHandlerResponse;

        $rawResults = $helperInstance::getPage($request->page, $builder);
        $batchActions = $helperInstance::getBatchActions($rawResults);
        $results = $batchActions->read($accessPolicy);
//        foreach ($rawResults as $rawResult) {
//            if ($accessPolicy) {
//                $rawResult->setAccessPolicy($accessPolicy, AccessPolicyEndOfLife::UntilNextRead);
//            }
//            $results[] = $rawResult->autoRead();
//        }

        $perm = [];
        if ($request->loggedUser) {
            $perm = $request->loggedUser->attemptToGrantPermissions(
                $request->accessLevel,
                $request->targetComponent,
                $request->attemptToGrantPerms,
                null,
            );
        }

        $perm = array_unique($perm);

        $responseData = ['results' => $results,'perm' => $perm, 'maxPage' => $helperInstance::getAmountOfPages($builder)];
        $hookHandlerResponse = $schema->runWebItemActionHookHandlers(WebItemAction::Page, WebItemActionHook::TweakResponseData, [
            'data' => &$responseData,
            'request' => $request,
        ]);
        if ($hookHandlerResponse) return $hookHandlerResponse;

        return Response::ok($responseData);
    }

    public static function ls(Request $request): Response
    {
        $accessPolicy = $request->getTargetAccessPolicy(WebItemAction::List);
        if ($accessPolicy instanceof Response) return $accessPolicy;

        if (!$request->targetComponent) return Response::badRequest();

        $schema = Schema::get($request->targetComponent);
        $helperInstance = $schema->getItemInstance();
        $builder = $helperInstance::getQueryCaller();

        if ($request->accessLevel === AccessLevel::OnlyAdminUsers) {
            if (!$request->loggedUser) return Response::forbidden();
            $capability = $request->loggedUser->getAdminCapability($request->targetComponent, Permission::List->value);

        } else {
            $capability = $request->loggedUser
                ? $request->loggedUser?->getAppCapability($request->targetComponent, Permission::List->value)
                : LktPermissionController::getEnsuredPublicPermission($request->targetComponent, Permission::List->value);
        }

        if ($capability && $capability === RoleCapability::Owned) {
            $ownershipField = $schema->getOwnershipField();
            if ($ownershipField) {
                $builder->andIntegerEqual($ownershipField->getColumn(), $request->loggedUser->getId());
            }
        }

        $schema->filterBuilder($builder, $request->params);

        $hookHandlerResponse = $schema->runWebItemActionHookHandlers(WebItemAction::List, WebItemActionHook::PrepareQueryBuilder, [
            'query' => $builder,
            'request' => $request,
        ]);
        if ($hookHandlerResponse) return $hookHandlerResponse;

        $rawResults = $helperInstance::getMany($builder);
        $batchActions = $helperInstance::getBatchActions($rawResults);
        $results = $batchActions->read($accessPolicy);
//        foreach ($rawResults as $rawResult) {
//            if ($accessPolicy) {
//                $rawResult->setAccessPolicy($accessPolicy, AccessPolicyEndOfLife::UntilNextRead);
//            }
//            $results[] = $rawResult->autoRead();
//        }

        $perm = [];
        if ($request->loggedUser) {
            $perm = $request->loggedUser->attemptToGrantPermissions(
                $request->accessLevel,
                $request->targetComponent,
                $request->attemptToGrantPerms,
                null,
            );
        }

        $perm = array_unique($perm);

        $responseData = ['results' => $results,'perm' => $perm];
        $hookHandlerResponse = $schema->runWebItemActionHookHandlers(WebItemAction::List, WebItemActionHook::TweakResponseData, [
            'data' => &$responseData,
            'request' => $request,
        ]);
        if ($hookHandlerResponse) return $hookHandlerResponse;

        return Response::ok($responseData);
    }
}