<?php

namespace Lkt\Context;

class SessionContext
{
    protected static SessionContext|null $instance = null;
    protected array $data = [];

    protected function __construct()
    {
        $haystack = $_SESSION;
        foreach ($haystack as $key => $value) {
            if (str_starts_with($key, 'ctx:')) {
                $this->data[$key] = $value;
            }
        }
    }

    public static function getInstance()
    {
        if (!static::$instance) static::$instance = new static();
        return static::$instance;
    }

    public function __get($key)
    {
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }
        return null;
    }

    public function __set($key, $value)
    {
        $this->data[$key] = $value;
        $_SESSION["ctx:{$key}"] = $value;
    }
}