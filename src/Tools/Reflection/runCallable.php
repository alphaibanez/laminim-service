<?php

namespace Lkt\Tools\Reflection;

function runCallable(callable $callable, array $args)
{
    $reflectionMethod = new \ReflectionMethod($callable[0], $callable[1]);

    $params = $reflectionMethod->getParameters();

    $paramsKeys = array_map(function (\ReflectionParameter $param) {
        return $param->getName();
    }, $params);

    foreach (array_keys($args) as $key) {
        if (!in_array($key, $paramsKeys)) unset($args[$key]);
    }

    return call_user_func_array($callable, $args);
}