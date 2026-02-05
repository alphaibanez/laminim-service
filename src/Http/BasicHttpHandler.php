<?php

namespace Lkt\Http;

use Lkt\Controllers\LktPermissionController;
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

        return Response::ok(['id' => $request->targetInstance->getId()]);
    }

    public static function up(Request $request): Response
    {
        $accessPolicy = $request->getTargetAccessPolicy(WebItemAction::Update);
        if ($accessPolicy instanceof Response) return $accessPolicy;

        if ($accessPolicy) {
            $request->targetInstance->setAccessPolicy($accessPolicy, AccessPolicyEndOfLife::UntilNextWrite);
        }
        if ($request->payload && count($request->payload) > 0) {
            $request->targetInstance->autoUpdate($request->payload);

        } else {
            $request->targetInstance->autoUpdate($request->params);
        }

        if ($request->httpEventHandlers) HttpEventHandler::triggerEvent(HttpEvent::SuccessUpdate, $request->httpEventHandlers, []);

        return Response::ok(['id' => $request->targetInstance->getId()]);
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
        return Response::ok();
    }

    public static function pg(Request $request): Response
    {
        $accessPolicy = $request->getTargetAccessPolicy(WebItemAction::Page);
        if ($accessPolicy instanceof Response) return $accessPolicy;

        if (!$request->targetComponent) return Response::badRequest();

        if ($request->accessLevel === AccessLevel::OnlyAdminUsers) {
            if (!$request->loggedUser) return Response::forbidden();
            $capability = $request->loggedUser->getAdminCapability($request->targetComponent, 'ls');

        } else {
            $capability = $request->loggedUser
                ? $request->loggedUser?->getAppCapability($request->targetComponent, 'ls')
                : LktPermissionController::getEnsuredPublicPermission($request->targetComponent, 'ls');
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

        //@todo: check if is anonymous, get access level field from schema and filter not allowed results from query

        $hooks = $schema->getWebItemActionHookHandlers(WebItemAction::Page, WebItemActionHook::PrepareQueryBuilder);
        foreach ($hooks as $hook) {
            call_user_func_array($hook->queryBuilderHandlerCallable, [
                'query' => $builder,
            ]);
        }

        $rawResults = $helperInstance::getPage($request->page, $builder);
        $results = [];
        foreach ($rawResults as $rawResult) {
            if ($accessPolicy) {
                $rawResult->setAccessPolicy($accessPolicy, AccessPolicyEndOfLife::UntilNextRead);
            }
            $results[] = $rawResult->autoRead();
        }

        $perm = [];
        if ($request->loggedUser) {
            $perm = $request->loggedUser->attemptToGrantPermissions(
                $request->accessLevel,
                $request->targetComponent,
                $request->attemptToGrantPerms,
                null,
            );
        }

        return Response::ok([
            'results' => $results,
            'maxPage' => $helperInstance::getAmountOfPages($builder),
            'perm' => $perm
        ]);
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
            $capability = $request->loggedUser->getAdminCapability($request->targetComponent, 'ls');

        } else {
            $capability = $request->loggedUser
                ? $request->loggedUser?->getAppCapability($request->targetComponent, 'ls')
                : LktPermissionController::getEnsuredPublicPermission($request->targetComponent, 'ls');
        }

        if ($capability && $capability === RoleCapability::Owned) {
            $ownershipField = $schema->getOwnershipField();
            if ($ownershipField) {
                $builder->andIntegerEqual($ownershipField->getColumn(), $request->loggedUser->getId());
            }
        }

        $hooks = $schema->getWebItemActionHookHandlers(WebItemAction::List, WebItemActionHook::PrepareQueryBuilder);
        foreach ($hooks as $hook) {
            call_user_func_array($hook->queryBuilderHandlerCallable, [
                'query' => $builder,
            ]);
        }

        $rawResults = $helperInstance::getMany($builder);
        $results = [];
        foreach ($rawResults as $rawResult) {
            if ($accessPolicy) {
                $rawResult->setAccessPolicy($accessPolicy, AccessPolicyEndOfLife::UntilNextRead);
            }
            $results[] = $rawResult->autoRead();
        }

        $perm = [];
        if ($request->loggedUser) {
            $perm = $request->loggedUser->attemptToGrantPermissions(
                $request->accessLevel,
                $request->targetComponent,
                $request->attemptToGrantPerms,
                null,
            );
        }

        return Response::ok([
            'results' => $results,
            'perm' => $perm
        ]);
    }
}