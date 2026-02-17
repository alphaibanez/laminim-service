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

    public static function onPagePrepareQueryBuilder(callable $handler): static
    {
        return new static(WebItemAction::Page, WebItemActionHook::PrepareQueryBuilder, $handler);
    }

    public static function tweakPageResponseData(callable $handler): static
    {
        return new static(WebItemAction::Page, WebItemActionHook::TweakResponseData, $handler);
    }

    public static function onListPrepareQueryBuilder(callable $handler): static
    {
        return new static(WebItemAction::List, WebItemActionHook::PrepareQueryBuilder, $handler);
    }

    public static function tweakListResponseData(callable $handler): static
    {
        return new static(WebItemAction::List, WebItemActionHook::TweakResponseData, $handler);
    }

    public static function beforeCreate(callable $handler): static
    {
        return new static(WebItemAction::Create, WebItemActionHook::BeforeAction, $handler);
    }

    public static function tweakCreateResponseData(callable $handler): static
    {
        return new static(WebItemAction::Create, WebItemActionHook::TweakResponseData, $handler);
    }

    public static function onCreateSuccess(callable $handler): static
    {
        return new static(WebItemAction::Create, WebItemActionHook::Success, $handler);
    }

    public static function beforeUpdate(callable $handler): static
    {
        return new static(WebItemAction::Update, WebItemActionHook::BeforeAction, $handler);
    }

    public static function tweakUpdateResponseData(callable $handler): static
    {
        return new static(WebItemAction::Update, WebItemActionHook::TweakResponseData, $handler);
    }

    public static function onUpdateSuccess(callable $handler): static
    {
        return new static(WebItemAction::Update, WebItemActionHook::Success, $handler);
    }

    public static function tweakDropResponseData(callable $handler): static
    {
        return new static(WebItemAction::Drop, WebItemActionHook::TweakResponseData, $handler);
    }

    public static function onDropSuccess(callable $handler): static
    {
        return new static(WebItemAction::Drop, WebItemActionHook::Success, $handler);
    }

    public static function tweakReadResponseData(callable $handler): static
    {
        return new static(WebItemAction::Read, WebItemActionHook::TweakResponseData, $handler);
    }
}