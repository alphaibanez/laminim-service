<?php

namespace Lkt\Factory\Instantiator\ValueObjects;

use Lkt\Connectors\DatabaseConnections;
use Lkt\Connectors\Interfaces\DatabaseConnector;
use Lkt\Debug\VarDumper;
use Lkt\Factory\Schemas\Schema;
use Lkt\QueryBuilding\Query;

class ComponentDatabaseIntegration
{
//    private static array $cache = [];

    public string $component = '';
    public Schema $schema;
    public string|null $databaseConnectorName;
    public DatabaseConnector|null $databaseConnector;
    public Query|null $query;

    public function __construct(Schema|string $component)
    {
        $schema = $component instanceof Schema ? $component : Schema::get($component);
        $component = $schema->getComponent();

        if ($schema->getInstanceSettings()->getQueryCallerClassName() !== '') {
            $fqdn = $schema->getInstanceSettings()->getQueryCallerFQDN();
            if (class_exists($fqdn)) {
                $query = call_user_func_array([$fqdn, 'getCaller'], []);
            } else {
                if ($schema->isPivot()) {
                    $query = Query::table($schema->getTable());
                } else {
                    $query = null;
                }
            }

        } else {
            $query = Query::table($schema->getTable());
        }

        $connector = null;
        $connection = null;
        if (!$schema->hasCodedDataContext()) {
            $connector = $schema->getDatabaseConnector();
            if ($connector === '') $connector = DatabaseConnections::$defaultConnector;

            if ($connector && $query instanceof Query) {
                $query->setDatabaseConnector($connector);

                $connection = DatabaseConnections::get($connector);
                if ($connection) {
                    $query->setColumns($connection->extractSchemaColumns($schema));
                }
            }
        }

        $this->component = $component;
        $this->schema = $schema;
        $this->databaseConnectorName = $connector;
        $this->databaseConnector = $connection;
        $this->query = $query;
    }

    public static function from(Schema|string $component): static
    {
        return new static($component);
//        return static::$cache[$component] ??= new static($component);
    }
}