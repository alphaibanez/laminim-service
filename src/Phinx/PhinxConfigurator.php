<?php

namespace Lkt\Phinx;

use Lkt\Connectors\DatabaseConnections;
use Lkt\Connectors\MySQLConnector;

class PhinxConfigurator
{
    protected static $migrationPaths = [];
    protected static $seedsPaths = [];
    protected static $environments = [];
    protected static $migrationTable = 'phinxlog';
    protected static $defaultConnector = null;

    private static $defaultMigrationPath = '%%PHINX_CONFIG_DIR%%/db/migrations';
    private static $defaultSeedPath = '%%PHINX_CONFIG_DIR%%/db/seeds';

    public static function addDefaultMigrationPath(): void
    {
        if (!in_array(static::$defaultMigrationPath, static::$migrationPaths, true)) {
            static::$migrationPaths[] = static::$defaultMigrationPath;
        }
    }

    public static function addDefaultSeedPath(): void
    {
        if (!in_array(static::$defaultSeedPath, static::$migrationPaths, true)) {
            static::$seedsPaths[] = static::$defaultSeedPath;
        }
    }

    public static function addMigrationPath(string $path): void
    {
        if (!in_array($path, static::$migrationPaths, true)) {
            static::$migrationPaths[] = $path;
        }
    }

    public static function addSeedPath(string $path): void
    {
        if (!in_array($path, static::$migrationPaths, true)) {
            static::$seedsPaths[] = $path;
        }
    }

    public static function setMigrationTable(string $name): void
    {
        static::$migrationTable = $name;
    }

    public static function setDefaultConnector(string $name): void
    {
        static::$defaultConnector = $name;
    }

    public static function getConfig(): array
    {
        $environments = [];

        foreach (DatabaseConnections::getAllConnectors() as $name => $connector) {
            $cfg = [
                'host' => $connector->getHost(),
                'name' => $connector->getDatabase(),
                'user' => $connector->getUser(),
                'pass' => $connector->getPassword(),
                'port' => $connector->getPort(),
                'charset' => $connector->getCharset(),
            ];

            if ($connector instanceof MySQLConnector) {
                $cfg['adapter'] = 'mysql';
            }

            $environments[$name] = $cfg;
        }

        if (count($environments) > 0){
            if (static::$defaultConnector) {
                $defaultDatabase = static::$defaultConnector;
            } else {
                $defaultDatabase = array_keys($environments)[0];
            }
            $environments['default_environment'] = $defaultDatabase;
        }

        $environments['default_migration_table'] = static::$migrationTable;

        return [
            'paths' => [
                'migrations' => static::$migrationPaths,
                'seeds' => static::$seedsPaths,
            ],
            'environments' => $environments
        ];
    }
}