<?php

namespace Lkt\Config\Routing;

use Lkt\Generated\LktAuthenticationLogOrderBy;
use Lkt\Generated\LktAuthenticationLogQueryBuilder;
use Lkt\Http\BasicHttpHandler;
use Lkt\Http\Request;
use Lkt\Http\Routes\GetRoute;
use Lkt\Instances\LktAuthenticationLog;
use Lkt\WebItems\WebItemActionHookHandler;

class UsersRouting
{
    public static function setup(): void
    {
        GetRoute::admin('/admin-api/user/{userId}/sign-in/history/page-{page}', BasicHttpHandler::Page)
            ->setTargetAccessPolicy('sign-in-history')
            ->setPageValueParamsExtractionKey('page')
            ->setTargetComponent(LktAuthenticationLog::COMPONENT)
            ->addWebItemActionHookHandler(WebItemActionHookHandler::onPagePrepareQueryBuilder([static::class, 'signInHistory']))
        ;
    }

    public static function signInHistory(Request $request, LktAuthenticationLogQueryBuilder $query)
    {
        $query
            ->andAttemptedSuccessfullyIsTrue()
            ->orderBy(LktAuthenticationLogOrderBy::idDESC());
    }
}