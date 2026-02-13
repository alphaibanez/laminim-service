<?php

namespace Lkt\Connectors;

class RestCountriesConnector extends CurlConnector
{
    protected string $host = 'https://restcountries.com';
}