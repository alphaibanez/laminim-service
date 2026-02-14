<?php

namespace Lkt\Connectors;

use Lkt\Connectors\Curl\CurlResponse;
use Lkt\Connectors\Exceptions\InvalidOpenExchangeRatesAppIdException;

class OpenExchangeRatesConnector extends CurlConnector
{
    protected string $host = 'https://openexchangerates.org';

    protected string $appId = '';

    public function setAppId(string $appId): static
    {
        $this->appId = $appId;
        return $this;
    }

    public function query(string $url = '', array $args = [], string $method = 'GET'): CurlResponse
    {
        $queryData = [...$args];
        if ($this->appId) {
            $queryData['app_id'] = $this->appId;
        } else {
            throw InvalidOpenExchangeRatesAppIdException::getInstance($this->name);
        }

        return parent::query($url, $queryData, $method);
    }
}