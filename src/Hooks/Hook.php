<?php

namespace Lkt\Hooks;

final class Hook
{
    protected static array $hooks = [];

    public static function register(string $hook, string $action, callable $handler): bool
    {
        if (!isset(self::$hooks[$hook])) self::$hooks[$hook] = [];
        if (!isset(self::$hooks[$hook][$action])) self::$hooks[$hook][$action] = [];


        if (!in_array($handler, static::$hooks[$hook][$action], true)){
            static::$hooks[$hook][$action][] = $handler;
        }
        return true;
    }

    public static function run(string $hook, ...$args): HookResponse
    {
        $response = new HookResponse($hook);

        foreach (self::$hooks[$hook] as $name => $actions) {
            foreach ($actions as $action) {
                $response->triggerAction($name, $action, ...$args);
            }
        }

        return $response;
    }
}