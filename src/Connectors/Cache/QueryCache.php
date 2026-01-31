<?php

namespace Lkt\Connectors\Cache;

class QueryCache
{
    /** @var QueryResultsCache[] */
    protected static array $cache = [];

    /**
     * @param string $connector
     * @param string $query
     * @param $results
     * @return void
     */
    public static function set(string $connector, string $query, $results): void
    {
        $index = QueryResultsCache::buildCacheIndex($connector, $query);
        if (isset(static::$cache[$index]) && static::$cache[$index] instanceof QueryResultsCache) {
            static::$cache[$index]->update($results);
            return;
        }

        static::$cache[$index] = QueryResultsCache::create($connector, $query, $results);
    }

    /**
     * @param string $connector
     * @param string $query
     * @return bool
     */
    public static function isset(string $connector, string $query): bool
    {
        $index = QueryResultsCache::buildCacheIndex($connector, $query);
        return isset(static::$cache[$index]) && static::$cache[$index] instanceof QueryResultsCache;
    }

    /**
     * @param string $connector
     * @param string $query
     * @return QueryResultsCache
     */
    public static function get(string $connector, string $query): QueryResultsCache
    {
        $index = QueryResultsCache::buildCacheIndex($connector, $query);
        return static::$cache[$index];
    }

    /**
     * @return QueryResultsCache[]
     */
    public static function getCache(): array
    {
        return self::$cache;
    }
}