<?php

namespace Lkt\Connectors;

class RestCountriesConnector extends CurlConnector
{
    protected string $host = 'https://api.restcountries.com/countries/v5';
}