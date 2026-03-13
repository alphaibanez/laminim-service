<?php

namespace Lkt\Factory\Instantiator\Helpers;

class RecursiveReadController
{
    public $stack = [];
    public static RecursiveReadController|null $instance = null;

    public function log(
        string $component,
        string $accessPolicy,
        string|array|null $identifier,
    )
    {
        $key = static::getLogKey($component, $accessPolicy, $identifier);
        if (in_array($key, $this->stack)) return false;
        $this->stack[] = $key;
        return true;
    }

    public static function getInstance(): static
    {
        if (!static::$instance) static::$instance = new static();
        return static::$instance;
    }

    public static function endStack(
        string $component,
        string $accessPolicy,
        string|array|null $identifier,
    ): void
    {
        $key = static::getLogKey($component, $accessPolicy, $identifier);
        if ($key === static::$instance->stack[0]) {
            static::$instance = null;
        }
    }


    public static function getLogKey(
        string $component,
        string $accessPolicy,
        string|array|null $identifier,
    )
    {
        return "{$component}-{$accessPolicy}-{$identifier}";
    }
}