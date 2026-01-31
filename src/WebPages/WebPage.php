<?php

namespace Lkt\WebPages;

use Lkt\WebItems\Enums\WebItemAction;

class WebPage
{
    protected static array $WEB_PAGES = [];

    /** @var WebItemAction[] */
    protected array $enabledAppActions = [];

    /** @var WebItemAction[] */
    protected array $enabledAdminActions = [];

    protected array $appAccessPoliciesPerAction = [];

    protected array $adminAccessPoliciesPerAction = [];

    public function __construct(
        readonly public string $component,
        readonly public string|null $publicComponentName = null,
    )
    {}

    public function setAppActionAccessPolicy(WebItemAction $action, string $accessPolicy): static
    {
        $this->appAccessPoliciesPerAction[$action->value] = $accessPolicy;
        return $this;
    }

    public function getAppActionAccessPolicy(WebItemAction $action): string
    {
        return trim($this->appAccessPoliciesPerAction[$action->value]);
    }

    public function setAdminActionAccessPolicy(WebItemAction $action, string $accessPolicy): static
    {
        $this->adminAccessPoliciesPerAction[$action->value] = $accessPolicy;
        return $this;
    }

    public function getAdminActionAccessPolicy(WebItemAction $action): string
    {
        return trim($this->adminAccessPoliciesPerAction[$action->value]);
    }

    /** @param WebItemAction[] $actions */
    public function setEnabledAppActions(array $actions): static
    {
        $this->enabledAppActions = $actions;
        return $this;
    }

    /** @param WebItemAction[] $actions */
    public function setEnabledAdminActions(array $actions): static
    {
        $this->enabledAdminActions = $actions;
        return $this;
    }

    public function getEnabledAppActions(): array
    {
        return $this->enabledAppActions;
    }

    public function getEnabledAdminActions(): array
    {
        return $this->enabledAdminActions;
    }

    public static function define(string $component, string|null $publicComponentName = null): static
    {
        return new static($component, $publicComponentName);
    }

    public static function register(self $webItem): void
    {
        static::$WEB_PAGES[$webItem->component] = $webItem;
    }

    public static function defineAndRegister(string $component, string|null $publicComponentName = null): static
    {
        $ins = static::define($component, $publicComponentName);
        static::register($ins);
        return $ins;
    }

    public static function detectWebItem(string $component):?static
    {
        if (isset(static::$WEB_PAGES[$component])) return static::$WEB_PAGES[$component];

        $filtered = array_filter(static::$WEB_PAGES, function (WebPage $item) use ($component) {
            return $item->publicComponentName === $component;
        });

        if (count($filtered) > 0) {
            $filtered = array_values($filtered);
            return $filtered[0];
        }

        return null;
    }

    public static function getAll(): array
    {
        return static::$WEB_PAGES;
    }
}