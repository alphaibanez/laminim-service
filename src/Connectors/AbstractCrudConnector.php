<?php

namespace Lkt\Connectors;

abstract class AbstractCrudConnector
{
    protected string $name;
    protected string $host = '';
    protected string $user = '';
    protected string $password = '';
    protected string $database = '';
    protected int $port = 0;
    protected string $charset = '';
    protected array $headers = [];
    protected $connection = null;
    protected bool $ignoreCache = false;
    protected bool $forceRefresh = false;
    
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function define(string $name): static
    {
        return new static($name);
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    
    public function setHost(string $host): static
    {
        $this->host = $host;
        return $this;
    }

    public function setCharset(string $charset): static
    {
        $this->charset = $charset;
        return $this;
    }

    public function setDatabase(string $database): static
    {
        $this->database = $database;
        return $this;
    }

    public function getCharset(): string
    {
        return $this->charset;
    }

    public function getDatabase(): string
    {
        return $this->database;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function setPort(int $port): static
    {
        $this->port = $port;
        return $this;
    }

    public function setUser(string $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function setHeaders(array $headers): static
    {
        $this->headers = $headers;
        return $this;
    }
}