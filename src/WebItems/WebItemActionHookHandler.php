<?php

namespace Lkt\WebItems;

use Lkt\WebItems\Enums\WebItemAction;
use Lkt\WebItems\Enums\WebItemActionHook;

class WebItemActionHookHandler
{
    protected function __construct(
        readonly public WebItemAction     $action,
        readonly public WebItemActionHook $hook,
        readonly public mixed             $queryBuilderHandlerCallable
    )
    {
    }

    public static function onPagePrepareQueryBuilder(callable $queryBuilderHandler): static
    {
        return new static(WebItemAction::Page, WebItemActionHook::PrepareQueryBuilder, $queryBuilderHandler);
    }

    public static function onListPrepareQueryBuilder(callable $queryBuilderHandler): static
    {
        return new static(WebItemAction::List, WebItemActionHook::PrepareQueryBuilder, $queryBuilderHandler);
    }
}