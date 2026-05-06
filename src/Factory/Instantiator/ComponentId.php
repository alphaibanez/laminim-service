<?php

namespace Lkt\Factory\Instantiator;

class ComponentId
{
    protected static $componentToId = [];
    protected static $idToComponent = [];

    public static function add(int $componentId, string $componentCode)
    {
        static::$componentToId[$componentCode] = $componentId;
        static::$idToComponent[$componentId] = $componentCode;
    }

    public static function getId(string $component)
    {
        return static::$componentToId[$component];
    }

    public static function getComponent(int $id)
    {
        return static::$idToComponent[$id];
    }
}