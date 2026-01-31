<?php

namespace Lkt\Hooks;

class HookResponse
{
    protected string $name = '';
    protected array $triggeredActions = [];

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function triggerAction(string $action, callable $handler, ...$args): static
    {
        $this->triggeredActions[$action][] = call_user_func_array($handler, $args);
        return $this;
    }
}