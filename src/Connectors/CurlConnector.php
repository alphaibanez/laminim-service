<?php

namespace Lkt\Connectors;

class CurlConnector extends AbstractCrudConnector
{
    /** @var CurlConnector[] */
    protected static array $connectors = [];

    public static function define(string $name): static
    {
        $r = new static($name);
        static::$connectors[$name] = $r;
        return $r;
    }

    public static function get(string $name): CurlConnector
    {
        if (!isset(static::$connectors[$name])) {
            throw new \Exception("Connector '{$name}' doesn't exists");
        }
        return static::$connectors[$name];
    }

    /**
     * @return CurlConnector[]
     */
    public static function getAllConnectors(): array
    {
        return static::$connectors;
    }
    private const userAgent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)';

    public function query(string $url = '', array $args = [], string $method = 'GET')
    {
        $ch = curl_init();

        if (!$method) {
            $method = 'GET';
        }

        curl_setopt($ch, CURLOPT_USERAGENT, static::userAgent);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        if ($this->user && $this->password){
            curl_setopt($ch, CURLOPT_USERPWD,  "{$this->user}:{$this->password}");
        }

        $link = $this->host . $url;
        $fieldsString = count($args) > 0 ? http_build_query($args) : '';

        if (count($args) > 0) {
            if ($method === 'GET') {
                $link .= '?' . $fieldsString;
            } elseif ($method === 'POST') {
                curl_setopt($ch, CURLOPT_POST, count(explode('&', urldecode($fieldsString))) > 0);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $fieldsString);
            } elseif ($method === 'DELETE') {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $fieldsString);
            }
        }

        if ($method === 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        }

        curl_setopt($ch, CURLOPT_URL, $link);

        if (count($this->headers) > 0) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        }

        $result = curl_exec($ch);

        //close connection
        curl_close($ch);

        return $result;
    }
}