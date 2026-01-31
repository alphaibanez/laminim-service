<?php

namespace Lkt\Connectors;

use Lkt\Connectors\Enums\RevolutApiVersion;
use Lkt\Connectors\Traits\Revolut\CustomerTrait;
use Lkt\Connectors\Traits\Revolut\OrdersTrait;
use Lkt\Connectors\Traits\Revolut\WebhookTrait;

class RevolutConnector
{
    use OrdersTrait,
        CustomerTrait,
        WebhookTrait;

    protected string $clientId = '';
    protected string $clientSecret = '';

    protected RevolutApiVersion $apiVersion = RevolutApiVersion::V_2025_10_16;

    protected bool $sandbox = false;


    /** @var RevolutConnector[] */
    protected static array $connectors = [];

    public static function define(string $name): static
    {
        $r = new static($name);
        static::$connectors[$name] = $r;
        return $r;
    }

    public static function get(string $name): RevolutConnector
    {
        if (!isset(static::$connectors[$name])) {
            throw new \Exception("RevolutConnector '{$name}' doesn't exists");
        }
        return static::$connectors[$name];
    }

    /**
     * @return RevolutConnector[]
     */
    public static function getAllConnectors(): array
    {
        return static::$connectors;
    }

    public function setClientId(string $clientId): static
    {
        $this->clientId = $clientId;
        return $this;
    }

    public function setClientSecret(string $clientSecret): static
    {
        $this->clientSecret = $clientSecret;
        return $this;
    }

    public function setSandbox(bool $sandbox = true): static
    {
        $this->sandbox = $sandbox;
        return $this;
    }
}