<?php

namespace Lkt\WebItems;

use Lkt\WebItems\Enums\WebItemAction;
use Lkt\WebItems\Enums\WebItemActionHook;

class WebItemActionHookHandler
{
    protected function __construct(
        readonly public WebItemAction     $action,
        readonly public WebItemActionHook $hook,
        readonly public mixed             $handler
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

    public static function onCreateSuccess(callable $handler): static
    {
        return new static(WebItemAction::Create, WebItemActionHook::Success, $handler);
    }

    public static function onUpdateSuccess(callable $handler): static
    {
        return new static(WebItemAction::Update, WebItemActionHook::Success, $handler);
    }

    public static function onDropSuccess(callable $handler): static
    {
        return new static(WebItemAction::Drop, WebItemActionHook::Success, $handler);
    }
}