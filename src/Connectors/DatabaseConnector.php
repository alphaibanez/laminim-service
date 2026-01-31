<?php

namespace Lkt\Connectors;

use Lkt\Factory\Schemas\Schema;
use Lkt\QueryBuilding\Query;

abstract class DatabaseConnector
{
    protected string $name;
    protected string $host = '';
    protected string $user = '';
    protected string $password = '';
    protected string $database = '';
    protected int $port = 0;
    protected string $charset = '';
    protected $connection = null;
    protected $ignoreCache = false;
    protected bool $forceRefresh = false;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function define(string $name): static
    {
        return new static($name);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setHost(string $host): static
    {
        $this->host = $host;
        return $this;
    }

    public function setCharset(string $charset): static
    {
        $this->charset = $charset;
        return $this;
    }

    public function setDatabase(string $database): static
    {
        $this->database = $database;
        return $this;
    }

    public function getCharset(): string
    {
        return $this->charset;
    }

    public function getDatabase(): string
    {
        return $this->database;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function setPort(int $port): static
    {
        $this->port = $port;
        return $this;
    }

    public function setUser(string $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function forceRefreshNextQuery(): static
    {
        $this->forceRefresh = true;
        return $this;
    }

    protected function forceRefreshFinished(): static
    {
        $this->forceRefresh = false;
        return $this;
    }

    abstract public function connect(): self;
    abstract public function disconnect(): self;
    abstract public function query(string $query, array $replacements = []):? array;
    abstract public function extractSchemaColumns(Schema $schema): array;
    abstract public function getLastInsertedId(): int;
    abstract public function makeUpdateParams(array $params = [], string $type = 'create') :string;
    abstract public function getQuery(Query $builder, string $type, string $countableField = null): string;
    abstract public function prepareDataToStore(Schema $schema, array $data): array;

    public function escapeDatabaseCharacters(string $str): string
    {
        $str = str_replace('\\', ':LKT_SLASH:', $str);
//        $str = str_replace('?', ':LKT_QUESTION_MARK:', $str);
//        return trim(str_replace("'", ':LKT_SINGLE_QUOTE:', $str));
        return trim($str);
    }

    public function unEscapeDatabaseCharacters(string $value): string
    {
        $value = str_replace(':LKT_SLASH:', '\\', $value);
        $value = str_replace(':LKT_QUESTION_MARK:', '?', $value);
        $value = str_replace(':LKT_SINGLE_QUOTE:', "'", $value);
        return trim(str_replace('\"', '"', $value));
    }

    final public function getSelectQuery(Query $builder): string
    {
        return $this->getQuery($builder, 'select');
    }

    final public function getSelectDistinctQuery(Query $builder): string
    {
        return $this->getQuery($builder,'selectDistinct');
    }

    final public function getCountQuery(Query $builder, string $countableField): string
    {
        return $this->getQuery($builder,'count', $countableField);
    }

    final public function getInsertQuery(Query $builder): string
    {
        return $this->getQuery($builder,'insert');
    }

    final public function getUpdateQuery(Query $builder): string
    {
        return $this->getQuery($builder,'update');
    }

    final public function getDeleteQuery(Query $builder): string
    {
        return $this->getQuery($builder,'delete');
    }
}