<?php

namespace Lkt\Connectors;

use Lkt\Connectors\Cache\QueryCache;
use Lkt\Connectors\Exceptions\InvalidDatabaseConnectorException;
use Lkt\Debug\VarDumper;
use Lkt\Factory\Instantiator\Enums\BatchInsertMode;
use Lkt\Factory\Instantiator\Instances\AbstractInstance;
use Lkt\Factory\Schemas\ComputedFields\AbstractComputedField;
use Lkt\Factory\Schemas\Fields\AbstractField;
use Lkt\Factory\Schemas\Fields\BooleanField;
use Lkt\Factory\Schemas\Fields\ConcatField;
use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\EmailField;
use Lkt\Factory\Schemas\Fields\FileField;
use Lkt\Factory\Schemas\Fields\FloatField;
use Lkt\Factory\Schemas\Fields\ForeignKeyField;
use Lkt\Factory\Schemas\Fields\HTMLField;
use Lkt\Factory\Schemas\Fields\IntegerField;
use Lkt\Factory\Schemas\Fields\JSONField;
use Lkt\Factory\Schemas\Fields\PivotField;
use Lkt\Factory\Schemas\Fields\RelatedField;
use Lkt\Factory\Schemas\Fields\RelatedKeysField;
use Lkt\Factory\Schemas\Fields\StringField;
use Lkt\Factory\Schemas\Fields\UnixTimeStampField;
use Lkt\Factory\Schemas\Schema;
use Lkt\Locale\Locale;
use Lkt\QueryBuilding\Constraints\AbstractConstraint;
use Lkt\QueryBuilding\Query;

class MariaDBConnector extends DatabaseConnector
{
    protected int $port = 3306;
    protected string $charset = 'utf8mb4';
    protected string $rememberTotal = '';


    /** @var MariaDBConnector[] */
    protected static array $connectors = [];

    public static function define(string $name): static
    {
        $r = new static($name);
        DatabaseConnections::set($r);
        static::$connectors[$name] = $r;
        return $r;
    }

    public static function get(string $name): static
    {
        if (!isset(static::$connectors[$name])) {
            throw InvalidDatabaseConnectorException::getInstance($name);
        }
        return static::$connectors[$name];
    }

    public function setRememberTotal(string $rememberTotal): static
    {
        $this->rememberTotal = $rememberTotal;
        return $this;
    }

    public function connect(): static
    {
        if ($this->connection !== null) {
            return $this;
        }

        // Perform the connection
        try {
            $this->connection = new \PDO (
                "mysql:host={$this->host}:{$this->port};dbname={$this->database};charset={$this->charset}",
                $this->user,
                $this->password
            );

        } catch (\Exception $e) {
            die ('Connection to database failed');
        }
        return $this;
    }

    public function disconnect(): static
    {
        $this->connection = null;
        return $this;
    }

    public function query(string $query, array $replacements = []):? array
    {
        $this->connect();
        $sql = ConnectionHelper::sanitizeQuery($query);
        $sql = \str_replace('_LANG', '_' . Locale::getLangCode(), $sql);
        if ($this->rememberTotal !== '') {
            $sql = \preg_replace('/SELECT/i', 'SELECT SQL_CALC_FOUND_ROWS', $sql, 1);
            $sql .= '; SET @rows_' . $this->rememberTotal . ' = FOUND_ROWS();';
        }

        $sql = trim($sql);
        $isSelect = strpos(strtolower($sql), 'select') === 0;

        // check if cached (only select queries)
        if ($isSelect && !$this->forceRefresh && !$this->ignoreCache && QueryCache::isset($this->name, $sql)) {
            return QueryCache::get($this->name, $sql)->getLatestResults();
        }

        // fetch
        $result = $this->connection->query($sql, \PDO::FETCH_ASSOC);

        if ($this->forceRefresh) $this->forceRefreshFinished();

        if ($result === true || $result === false) {
            QueryCache::set($this->name, $sql, null);
            return null;
        }

        $r = [];
        foreach ($result as $row) {
            $r[] = $row;
        }

        QueryCache::set($this->name, $sql, $r);
        return $r;
    }

    public function extractSchemaColumns(Schema $schema): array
    {
        $table = $schema->getTable();

        /** @var AbstractField[] $fields */
        $fields = $schema->getSameTableFields();

        $r = [];

        foreach ($fields as $key => $field) {
            if ($field instanceof PivotField || $field instanceof RelatedField || $field instanceof RelatedKeysField || $field instanceof AbstractComputedField || $field instanceof ConcatField) {
                continue;
            }
            $column = trim($field->getColumn());
            if ($field instanceof JSONField && $field->isCompressed()) {
                $r[] = "UNCOMPRESS({$table}.{$column}) as {$key}";
            } else {
                $r[] = "{$table}.{$column} as {$key}";
            }
        }

        return $r;
    }

    private function buildColumns(Query $builder): string
    {
        $r = [];
        $table = $builder->getTableNameOrAlias();
        foreach ($builder->getColumns() as $column) $r[] = $this->buildColumnString($column, $table);

        return implode(',', $r);
    }


    private function buildColumnString(string $column, string $table): string
    {
        $prependTable = $table !== '' ? "{$table}." : '';
        $tempColumn = str_replace([' as ', ' AS ', ' aS ', ' As '], '{{---LKT_SEPARATOR---}}', $column);
        $exploded = explode('{{---LKT_SEPARATOR---}}', $tempColumn);

        $key = trim($exploded[0]);
        $alias = isset($exploded[1]) ? trim($exploded[1]) : '';

        $schema = Schema::getFromTable($table);
        $field = $schema->getField($alias);
        if (!$field) $field = $schema->getField($key);

        if ($field instanceOf StringField && method_exists($field, 'isI18nJson') && $field->isI18nJson()) {
            $lang = $field->hasFixedLangKey() ? $field->getFixedLangKey() : Locale::getLangCode();
            if (!$lang) $lang = 'en';

            return "JSON_UNQUOTE(JSON_EXTRACT({$key}, \"$.{$lang}\")) as {$alias}";
        }

        if (!$field) return $column;

        if (str_starts_with($column, 'UNCOMPRESS') || str_starts_with($column, "'") || str_starts_with($column, "DISTINCT") || strpos($column, '(') > 0) {
            if ($alias !== '') {
                $r = "{$key} AS {$alias}";
            } else {
                $r = $key;
            }
        }

        elseif (str_starts_with($key, $prependTable)) {
            if ($alias !== '') {
                $r = "{$key} AS {$alias}";
            } else {
                $r = $key;
            }
        } else {
            if ($alias !== '') {
                $r = "{$prependTable}{$key} AS {$alias}";
            } else {
                $r = "{$prependTable}{$key}";
            }
        }

        return $r;
    }

    public function makeUpdateParams(array $params = [], string $type = 'insert') :string
    {
        $r = [];
        $parsed = $this->makeUpdateParamsArray($params, $type);
        foreach ( $parsed as $field => $value) {
            $r[] = "`{$field}`={$value}";
        }
//        foreach ($params as $field => $value) {
////            $v = addslashes(stripslashes($value));
//            $v = $value;
//            if (strpos($value, 'JSON_SET(') === 0) {
//                if ($type === 'create' || $type === 'insert') {
//                    $value = str_replace($field, '"{}"', $value);
//                }
//                $r[] = "`{$field}`={$value}";
//            }
//            elseif (strpos($value, 'COMPRESS(') === 0){
//                $r[] = "`{$field}`={$value}";
//            }
//            else {
//                $r[] = "`{$field}`='{$v}'";
//            }
//        }

        return trim(implode(',', $r));
    }

    public function makeUpdateParamsArray(array $params = [], string $type = 'insert') :array
    {
        $r = [];
        foreach ($params as $field => $value) {
//            $v = addslashes(stripslashes($value));
            $v = $value;
            if (strpos($value, 'JSON_SET(') === 0) {
                if ($type === 'create' || $type === 'insert') {
                    $value = str_replace($field, '"{}"', $value);
                }
                $r[$field] = "{$value}";
            }
            elseif (strpos($value, 'COMPRESS(') === 0){
                $r[$field] = "{$value}";
            }
            else {
                $r[$field] = "'{$v}'";
            }
        }

        return $r;
    }

    public function getLastInsertedId(): int
    {
        if ($this->connection === null) return 0;
        return (int)$this->connection->lastInsertId();
    }

    public function getQuery(Query $builder, string $type, string $countableField = null): string
    {
        $whereString = $builder->getQueryWhere($this);

        switch ($type) {
            case 'select':
            case 'selectDistinct':
            case 'count':
                $from = [];
                foreach ($builder->getJoins() as $join) {
                    $from[] = (string)$join;
                }

                $joinedWhere = [];
                $joinedBuilders = $builder->getJoinedBuilders();
                if (count($joinedBuilders) > 0) {
                    foreach ($joinedBuilders as $key => $joinedBuilder) {
                        $joinData = $builder->getJoinedBuildersRelation($key);
                        $from[] = $joinedBuilder->getJoinString($joinData[0], $joinData[1], $builder->formatJoinedColumn($joinData[2]));
                    }
                }
                $fromString = implode(' ', $from);
                $fromString = str_replace('{{---LKT_PARENT_TABLE---}}', $builder->getTable(), $fromString);

                if (count($joinedWhere) > 0) {
                    $whereString = implode(' AND ', [$whereString, implode(' AND ', $joinedWhere)]);
                }

                $distinct = '';

                if ($type === 'selectDistinct') {
                    $distinct = 'DISTINCT';
                    $type = 'select';
                }

                elseif ($type === 'count') {
                    $distinct = 'DISTINCT';
                }

                $tableAlias = $builder->getTableAlias();
                $asTableAlias = $builder->hasTableAlias() ? " AS {$tableAlias} " : '';

                if ($type === 'select') {
                    $columns = $this->buildColumns($builder);
                    $orderBy = '';
                    $groupBy = '';
                    $pagination = '';

                    if ($builder->hasOrder()) $orderBy = " ORDER BY {$builder->getOrder()}";
                    if ($builder->hasGroupBy()) $groupBy = " GROUP BY {$builder->getGroupBy()}";

                    if ($builder->hasPagination()) {
                        $p = $builder->getPage() * $builder->getLimit();
                        $pagination = " LIMIT {$p}, {$builder->getLimit()}";

                    } elseif ($builder->hasLimit()) {
                        $pagination = " LIMIT {$builder->getLimit()}";
                    }

                    if ($orderBy && $groupBy) {
                        $r = "SELECT * FROM (SELECT {$distinct} {$columns} FROM {$builder->getTable()} {$fromString} WHERE 1 {$whereString} {$orderBy} {$pagination}) AS tmp_table GROUP BY {$groupBy}";
                    } else {
                        $r = "SELECT {$distinct} {$columns} FROM {$builder->getTable()}{$asTableAlias} {$fromString} WHERE 1 {$whereString} {$orderBy} {$groupBy} {$pagination}";
                    }

                    $r = str_replace('DISTINCT DISTINCT',  'DISTINCT', $r);
                    return $r;
                }

                if ($type === 'count') {
                    return "SELECT COUNT({$distinct} {$countableField}) AS Count FROM {$builder->getTable()}{$asTableAlias} {$fromString} WHERE 1 {$whereString}";
                }
                return '';

            case 'update':
            case 'insert':
            case 'insert-ignore':
                $data = $this->makeUpdateParams($builder->getData(), $type);

                if ($type === 'update') {
                    return "UPDATE {$builder->getTable()} SET {$data} WHERE 1 {$whereString}";
                }

                if ($type === 'insert') {
                    return "INSERT INTO {$builder->getTable()} SET {$data}";
                }

                if ($type === 'insert-ignore') {
                    return "INSERT IGNORE INTO {$builder->getTable()} SET {$data}";
                }
                return '';

            case 'delete':
                return "DELETE FROM {$builder->getTable()} WHERE 1 {$whereString}";

            default:
                return '';
        }
    }

    public function prepareDataToStore(Schema $schema, array $data): array
    {
        $fields = $schema->getAllFields();
        $parsed = [];

        $fixedLangFields = array_filter($fields, function (AbstractField $field) {
            return $field instanceof StringField && $field->isI18nJson() && $field->getFixedLangKey();
        });

        $multiLangSharedKeys = [];
        foreach ($fixedLangFields as $field) {
            $c = $field->getColumn();
            if (!is_array($multiLangSharedKeys[$c])) $multiLangSharedKeys[$c] = [];
            $multiLangSharedKeys[$c][] = $field->getName();
        }

        $groupedTranslations = [];

        foreach ($fields as $column => $field) {
            $columnKey = $column;
            if ($field instanceof ForeignKeyField) {
                $columnKey .= 'Id';
            }

            if (array_key_exists($columnKey, $data)){
                $value = $data[$columnKey];

                $compress = $field instanceof JSONField && $field->isCompressed();

                if ($field instanceof StringField && $field->isI18nJson()) {
                    $r = trim($value);

                    $lang = $field->hasFixedLangKey() ? $field->getFixedLangKey() : Locale::getLangCode();
                    if (!$lang) $lang = 'en';
                    $column = $field->getColumn();

//                    $value = "JSON_SET({$column}, \"$.{$lang}\", \"{$r}\")";

                    $groupedTranslations[$column][$lang] = $r;
                    continue;
                }

                if ($field instanceof StringField
                    || $field instanceof EmailField
                    || $field instanceof RelatedKeysField
                    || $field instanceof ForeignKeyField
                ) {
                    $r = trim($value);
                    if ($compress) {
                        $value = "COMPRESS('{$r}')";
                    } else {
                        $value = $r;
                    }
                }

                if ($field instanceof HTMLField) {
                    $r = $this->escapeDatabaseCharacters($value);
                    if ($compress) {
                        $value = "COMPRESS('{$r}')";
                    } else {
                        $value = $r;
                    }
                }

                if ($field instanceof BooleanField) {
                    $value = $value === true ? 1 : 0;
                }

                if ($field instanceof IntegerField && $field->isMultiple()) {
                    $valueAux = [];
                    foreach ($value as $item) {
                        $valueAux[] = (int)$item;
                    }
                    $value = implode(';', $valueAux);
                }

                else if ($field instanceof IntegerField) {
                    $value = (int)$value;
                }

                if ($field instanceof FloatField && $field->isMultiple()) {
                    $valueAux = [];
                    foreach ($value as $item) {
                        $valueAux[] = (float)$item;
                    }
                    $value = implode(';', $valueAux);
                }

                else if ($field instanceof FloatField) {
                    $value = (float)$value;
                }

                if ($field instanceof UnixTimeStampField) {
                    if ($value instanceof \DateTime) {
                        $value = strtotime($value->format('Y-m-d H:i:s'));
                    } else {
                        $value = 0;
                    }
                }

                if ($field instanceof DateTimeField) {
                    if ($value instanceof \DateTime) {
                        $value = $value->format('Y-m-d H:i:s');
                    } else {
                        $value = '0000-00-00 00:00:00';
                    }
                }

                if ($field instanceof FileField && $field->isMultiple()) {
                    $valueAux = [];
                    foreach ($value as $item) {
                        $valueAux[] = $item;
                    }
                    $value = implode(';', $valueAux);
                }
                else if ($field instanceof FileField) {
                    if (is_object($value)) {
                        $value = $value->name;
                    } else {
                        $value = '';
                    }
                }

                if ($field instanceof JSONField) {
                    if (is_array($value)){
                        if (!$field->isI18nJson()) {
                            $v = json_encode($value, JSON_UNESCAPED_UNICODE);
                            $v = $this->escapeDatabaseCharacters($v);
                            $v = htmlspecialchars($v, JSON_UNESCAPED_UNICODE|ENT_QUOTES, 'UTF-8');

                        } else {
                            foreach ($value as $k => &$v) {
                                $v = $this->escapeDatabaseCharacters($v);
                                $v = htmlspecialchars($v, JSON_UNESCAPED_UNICODE|ENT_QUOTES, 'UTF-8');
                            }

                            $v = json_encode($value, JSON_UNESCAPED_UNICODE);
                        }

                        if ($compress) {
                            $v = "COMPRESS('{$v}')";
                        }
                        $value = $v;
                    }
                }

                $parsed[$field->getColumn()] = $value;
            }
        }

        foreach ($groupedTranslations as $column => $langs) {
            $t = [];
            foreach ($langs as $lang => $val) {
                $t[] = "\"$.{$lang}\"";
                $t[] = "\"{$val}\"";
            }

            if (count($t) === 0) continue;

            $t = implode(', ', $t);
            $t = "JSON_SET({$column}, {$t})";

            $parsed[$column] = $t;
        }

        return $parsed;
    }

    public function prepareWhereConstraint(AbstractConstraint $whereConstraint): AbstractConstraint
    {
        $col = $whereConstraint->getColumn();
        if (strpos($col, '__loc:') === 0) {
            $col = substr($col, 6);
            $lang = substr($col, 0, 2);
            $col = substr($col, 3);

            $table = $whereConstraint->getTable();
            if ($table) {
                $col = "{$table}.{$col}";
                $whereConstraint->setTable('');
            }

            $col = "JSON_UNQUOTE(JSON_EXTRACT({$col}, \"$.{$lang}\"))";
            $whereConstraint->setColumn($col);
        }
        return $whereConstraint;
    }

    public function getDatabases(): array
    {
        $r = [];
        $results = $this->query("SHOW DATABASES;");
        foreach ($results as $dbName) {
            $r[] = $dbName['Database'];
        }

        return $r;
    }

    public function batchInsert(array $items, Query $builder, Schema $schema, BatchInsertMode $mode = BatchInsertMode::onDuplicatedIgnore): static
    {
        $values = [];
        /** @var AbstractInstance $item */
        foreach ($items as $item) {
            $parsed = $this->prepareDataToStore($schema, $item->getUpdatedData());
            $builder->updateData($parsed);

            $values[] = $this->makeUpdateParamsArray($builder->getData(), 'create');
        }

        $valuesKeys = '(' . implode(',', array_keys($values[0])) . ')';
        $values = array_map(function (array $v) { return implode(', ', $v); }, $values);
        $valuesString = '(' . implode('),(', $values) . ')';

        $query = $mode === BatchInsertMode::onDuplicatedIgnore ? "INSERT IGNORE INTO" : "INSERT INTO";

        $query .= " {$schema->getTable()} $valuesKeys VALUES $valuesString";

        if ($mode === BatchInsertMode::onDuplicatedUpdate) {
            $updateKeys = [];
            $identifiers = array_map(function (AbstractField $f) { return $f->getColumn();}, $schema->getIdentifiers());
            $fields = array_map(function (AbstractField $f) { return $f->getColumn();}, $schema->getSameTableFields());
            $fields = array_values(array_filter($fields, function (string $f) use ($identifiers) {
                return !in_array($f, $identifiers);
            }));

            foreach ($fields as $field) {
                $updateKeys[] = "{$field} = VALUES({$field})";
            }

            if (count($updateKeys) > 0) {
                $updateKeysStr = implode(', ', $updateKeys);
                $query .= " ON DUPLICATE KEY UPDATE {$updateKeysStr}";
            }
        }

        $this->query($query);
        return $this;
    }

    public function batchUpdate(array $items, Schema $schema): static
    {
        $values = ['START TRANSACTION'];

        /** @var AbstractInstance $item */
        foreach ($items as $item) {
            $builder = $schema->getQueryBuilder();
            $parsed = $this->prepareDataToStore($schema, $item->getUpdatedData());
            $builder->updateData($parsed);
            $schema->applyIdentifierConstraintsToQueryFromInstance($builder, $item);

            $values[] = $this->getQuery($builder, 'update');
        }

        $values[] = 'COMMIT';

        $query = implode(';', $values);

        $this->query($query);
        return $this;
    }

    public function batchDrop(array $items, Query $builder, Schema $schema): static
    {
        $values = [];
        $identifiers = $schema->getIdentifiers();

        foreach ($items as $item) {
            $idValues = [];
            foreach ($identifiers as $identifier) {
                $getter = $identifier->getGetterForPrimitiveValue();
                $idValues[$identifier->getName()] = $item->{$getter}();
            }
            $parsed = $this->prepareDataToStore($schema, $idValues);
            $builder->updateData($parsed);

            $values[] = $this->makeUpdateParams($builder->getData(), 'create');
        }

        if (count($values) === 0) return $this;

        $valuesString = '(' . implode(') OR (', $values) . ')';

        $query = "DELETE FROM";

        $query .= " {$schema->getTable()} WHERE {$valuesString}";

        $this->query($query);
        return $this;
    }
}