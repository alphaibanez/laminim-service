<?php

namespace Lkt\Factory\Instantiator\ValueObjects;

use Lkt\Connectors\DatabaseConnections;
use Lkt\Connectors\DatabaseConnector;
use Lkt\Debug\VarDumper;
use Lkt\Factory\Schemas\Schema;
use Lkt\QueryBuilding\Query;

class ComponentDatabaseIntegration
{
//    private static array $cache = [];

    public string $component = '';
    public Schema $schema;
    public string $databaseConnectorName;
    public DatabaseConnector $databaseConnector;
    public Query $query;

    public function __construct(Schema|string $component)
    {
        $schema = $component instanceof Schema ? $component : Schema::get($component);
        $component = $schema->getComponent();

        if ($schema->getInstanceSettings()->getQueryCallerClassName() !== '') {
            $fqdn = $schema->getInstanceSettings()->getQueryCallerFQDN();
            $query = call_user_func_array([$fqdn, 'getCaller'], []);

        } else {
            $query = Query::table($schema->getTable());
        }

        $connector = $schema->getDatabaseConnector();
        if ($connector === '') $connector = DatabaseConnections::$defaultConnector;
        $connection = DatabaseConnections::get($connector);
        $query->setColumns($connection->extractSchemaColumns($schema));

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