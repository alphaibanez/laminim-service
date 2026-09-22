<?php

namespace Lkt\Connectors\Traits\Database;

use Lkt\Factory\Instantiator\Enums\DatabaseInsertMode;
use Lkt\QueryBuilding\Constraints\AbstractConstraint;
use Lkt\QueryBuilding\Query;

trait BaseDatabaseConnector
{
    protected string $name;
    protected string $host = '';
    protected string $user = '';
    protected string $password = '';
    protected string $database = '';
//    protected int $port = 0;
//    protected string $charset = '';

    protected $connection = null;
    protected $ignoreCache = false;
    protected bool $forceRefresh = false;

    protected function __construct(string $name)
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

    public function escapeDatabaseCharacters(string $str): string
    {
//        $str = str_replace('\\', ':LKT_SLASH:', $str);
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

    final public function getInsertQuery(Query $builder, DatabaseInsertMode $mode = DatabaseInsertMode::onDuplicatedIgnore): string
    {
        $action = $mode === DatabaseInsertMode::onDuplicatedIgnore ? 'insert-ignore' :  'insert';
        return $this->getQuery($builder,$action);
    }

    final public function getUpdateQuery(Query $builder): string
    {
        return $this->getQuery($builder,'update');
    }

    final public function getDeleteQuery(Query $builder): string
    {
        return $this->getQuery($builder,'delete');
    }

    public function prepareWhereConstraint(AbstractConstraint $whereConstraint): AbstractConstraint
    {
        return $whereConstraint;
    }
}