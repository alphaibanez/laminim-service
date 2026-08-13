<?php

namespace Lkt\Factory\Instance\Traits;

use Lkt\Factory\Instantiator\Cache\InstanceCache;
use Lkt\Factory\Instantiator\Exceptions\InvalidCountableFieldException;
use Lkt\Factory\Instantiator\Instances\AbstractInstance;
use Lkt\Factory\Instantiator\Instantiator;
use Lkt\Factory\Instantiator\ValueObjects\ComponentDatabaseIntegration;
use Lkt\Factory\Instantiator\ValueObjects\MonthlyAccuratePages;
use Lkt\Factory\Schemas\Exceptions\InvalidComponentException;
use Lkt\Factory\Schemas\Exceptions\InvalidSchemaAppClassException;
use Lkt\Factory\Schemas\Exceptions\SchemaNotDefinedException;
use Lkt\Factory\Schemas\Schema;
use Lkt\QueryBuilding\Query;
use Lkt\QueryBuilding\SelectBuilder;
use function Lkt\Tools\Pagination\getTotalPages;

trait ItemWithInstanceFactoryTrait
{
    /**
     * @throws InvalidComponentException
     * @throws SchemaNotDefinedException
     */
    public static function getInstance($id = null, array $initialData = []): static
    {
        if (!$id) {
            $r = new static($initialData);

            /** @var Schema $schema */
            $schema = $r->getSchema();
            $fields = $schema->getChoiceFieldsWithDefaultValue();

            if (count($fields)) {
                foreach ($fields as $field) {
                    $setter = $field->getSetterForPrimitiveValue();
                    $r->{$setter}($field->getEmptyDefault());
                }
            }

            $fields = $schema->getFieldsWithDefaultValue();

            if (count($fields)) {
                foreach ($fields as $field) {
                    $setter = $field->getSetterForPrimitiveValue();
                    $r->{$setter}($field->getDefaultValue());
                }
            }

            return $r;
        }

        $schema = Schema::get(static::COMPONENT);
        $code = is_array($id)
            ? $schema->getInstanceCode($id)
            : $schema->getInstanceCode([], $id)
        ;

        // Match anonymous instance feed with an array of data
        if (str_ends_with($code, '_')) {
            $r = new static($initialData);
            if (is_array($id) && count($initialData) === 0) {
                $r->feed($id);
            }
            InstanceCache::store($code, $r);
            return InstanceCache::load($code);
        }

        // Match already cached instance
        if (InstanceCache::inCache($code)) {
            $cached = InstanceCache::load($code);
            return $cached;
        }

        // Traditional blank scenery: instance when given a numeric id and an array of initial data
        if (count($initialData) > 0) {
            $r = new static();
            $r->feed($initialData);
            InstanceCache::store($code, $r);
            return InstanceCache::load($code);
        }

        // Database fetch
        $dbIntegration = ComponentDatabaseIntegration::from(static::COMPONENT);
        $builder = $dbIntegration->query;
        $schema = $dbIntegration->schema;

        $schema->applyIdentifierConstraintsToQueryFromData($builder, $id);

        $data = $builder->selectDistinct();
        if (count($data) > 0) {
            $r = new static($data[0]);
            InstanceCache::store($code, $r);
            return InstanceCache::load($code);
        }

        // Fallback anonymous instance
        $r = new static();
        $r->feed($initialData);
        return $r;
    }

    /**
     * @throws InvalidComponentException
     * @throws SchemaNotDefinedException
     */
    public static function getInstanceOrNull($id = null, array $initialData = []): static|null
    {
        $instance = static::getInstance($id, $initialData);
        if ($instance->isAnonymous()) return null;
        return $instance;
    }

    /**
     * @throws InvalidComponentException
     * @throws InvalidSchemaAppClassException
     * @throws SchemaNotDefinedException
     */
    public static function getMany(Query $query = null): array
    {
        if (!$query) {
            $query = static::getQueryBuilder();
        }
        return Instantiator::makeResults(static::COMPONENT, $query->selectDistinct());
    }

    /**
     * @return AbstractInstance|null
     * @throws InvalidComponentException
     * @throws InvalidSchemaAppClassException
     * @throws SchemaNotDefinedException
     */
    public static function getOne(Query $query = null)
    {
        if (!$query) $query = static::getQueryBuilder();
        $query->pagination(1, 1);
        $r = Instantiator::makeResults(static::COMPONENT, $query->selectDistinct());
        if (count($r) > 0) {
            return $r[0];
        }
        return null;
    }

    /**
     * @throws SchemaNotDefinedException
     */
    public static function getCount(Query $query = null, string $countableField = null): int
    {
        if (!$query) $query = static::getQueryBuilder();

        if (!$countableField) {
            $schema = Schema::get(static::COMPONENT);
            $countableField = $schema->getCountableField();
        }

        if (!$countableField) return 0;

        return $query->count($countableField);
    }

    /**
     * @throws SchemaNotDefinedException
     */
    public static function getAmountOfPages(Query $queryCaller = null, string $countableField = null, int $itemsPerPage = 0): int
    {
        $total = static::getCount($queryCaller, $countableField);
        if ($total === 0) return 0;
        $schema = Schema::get(static::COMPONENT);
        if ($itemsPerPage <= 0) $itemsPerPage = $schema->getItemsPerPage();
        if ($itemsPerPage <= 0) return 0;
        return getTotalPages($total, $itemsPerPage);
    }

    /**
     * @param int $page
     * @param Query|null $query
     * @return array
     * @throws InvalidComponentException
     * @throws InvalidSchemaAppClassException
     * @throws SchemaNotDefinedException
     */
    public static function getPage(int $page, Query $query = null, int $itemsPerPage = 0): array
    {
        if (!$query) $query = static::getQueryBuilder();
        $schema = Schema::get(static::COMPONENT);
        $limit = $itemsPerPage;
        if ($limit <= 0) $limit = $query->getLimit();
        if ($limit <= 0) $limit = $schema->getItemsPerPage();
        if ($limit >= 0) $query->pagination($page, $limit);
        return Instantiator::makeResults(static::COMPONENT, $query->selectDistinct());
    }


    /**
     * @param int $page
     * @param Query|null $queryCaller
     * @param string|null $countableField
     * @return array
     * @throws InvalidComponentException
     * @throws InvalidCountableFieldException
     * @throws InvalidSchemaAppClassException
     * @throws SchemaNotDefinedException
     */
    public static function getMonthlyAccuratePage(int $page, Query|null $queryCaller = null, string|null $countableField = null): array
    {
        if (!$queryCaller) $queryCaller = static::getQueryBuilder();
        $originalSelect = $queryCaller->getColumns();
        $pagesValueObject = static::getMonthlyAccuratePages($queryCaller, $countableField);
        $queryCaller->setColumns($originalSelect);
        $month = $pagesValueObject->getPageYearMonth($page);

        if (is_null($month)) {
            return [];
        }

        $queryCaller->andExtractYearMonthEqual($countableField, $month);
        return Instantiator::makeResults(static::COMPONENT, $queryCaller->selectDistinct());
    }

    /**
     * @param Query|null $query
     * @param string|null $countableField
     * @param int $itemsPerPage
     * @return MonthlyAccuratePages
     * @throws InvalidCountableFieldException
     * @throws SchemaNotDefinedException
     */
    public static function getMonthlyAccuratePages(Query|null $query = null, string|null $countableField = null): MonthlyAccuratePages
    {
        if (!$countableField) throw InvalidCountableFieldException::getInstance(__METHOD__, static::COMPONENT);

        if (!$query) $query = static::getQueryBuilder();

        $query->setColumns(SelectBuilder::extractYearMonthDatum($countableField, 'countable_datum'));

        $results = $query->selectDistinct();

        $data = array_unique(array_map(function ($item) {
            return (int)$item['countable_datum'];
        }, $results));

        return new MonthlyAccuratePages($data);
    }
}