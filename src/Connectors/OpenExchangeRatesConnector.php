<?php

namespace Lkt\Connectors;

class OpenExchangeRatesConnector extends CurlConnector
{
    protected string $host = 'https://openexchangerates.org';
}