<?php

namespace Lkt\Factory\Instance\Traits;

use Lkt\Factory\Instantiator\Cache\InstanceCache;
use Lkt\Factory\Instantiator\Instances\AbstractInstance;
use Lkt\Factory\Instantiator\Instantiator;
use Lkt\Factory\Instantiator\ValueObjects\ComponentDatabaseIntegration;
use Lkt\Factory\Schemas\Exceptions\InvalidComponentException;
use Lkt\Factory\Schemas\Exceptions\InvalidSchemaAppClassException;
use Lkt\Factory\Schemas\Exceptions\SchemaNotDefinedException;
use Lkt\Factory\Schemas\Schema;
use Lkt\QueryBuilding\Query;
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

            $schema = Schema::get(static::COMPONENT);
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

        $codeId = is_array($id) ? implode('-', $id) : $id;

        $schema = Schema::get(static::COMPONENT);
        $code = $schema->getInstanceCode([], $codeId);

        if (InstanceCache::inCache($code)) {
            $cached = InstanceCache::load($code);
            return $cached;
        }

        if (count($initialData) > 0) {
            $r = new static($initialData);
            InstanceCache::store($code, $r);
            return InstanceCache::load($code);
        }

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

        return new static($initialData);
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
     * @throws Exception
     */
    public static function getMany(Query $queryCaller = null): array
    {
        if (!$queryCaller) {
            $queryCaller = static::getQueryCaller();
        }
        return Instantiator::makeResults(static::COMPONENT, $queryCaller->selectDistinct());
    }

    /**
     * @return AbstractInstance|null
     * @throws InvalidComponentException
     * @throws InvalidSchemaAppClassException
     * @throws SchemaNotDefinedException
     */
    public static function getOne(Query $queryCaller = null)
    {
        if (!$queryCaller) $queryCaller = static::getQueryCaller();
        $queryCaller->pagination(1, 1);
        $r = Instantiator::makeResults(static::COMPONENT, $queryCaller->selectDistinct());
        if (count($r) > 0) {
            return $r[0];
        }
        return null;
    }

    /**
     * @throws SchemaNotDefinedException
     */
    public static function getCount(Query $queryCaller = null, string $countableField = null): int
    {
        if (!$queryCaller) $queryCaller = static::getQueryCaller();

        if (!$countableField) {
            $schema = Schema::get(static::COMPONENT);
            $countableField = $schema->getCountableField();
        }

        if (!$countableField) return 0;

        return $queryCaller->count($countableField);
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
     * @param Query|null $queryCaller
     * @return array
     * @throws InvalidComponentException
     * @throws InvalidSchemaAppClassException
     * @throws SchemaNotDefinedException
     */
    public static function getPage(int $page, Query $queryCaller = null, int $itemsPerPage = 0): array
    {
        if (!$queryCaller) $queryCaller = static::getQueryCaller();
        $schema = Schema::get(static::COMPONENT);
        $limit = $itemsPerPage;
        if ($limit <= 0) $limit = $queryCaller->getLimit();
        if ($limit <= 0) $limit = $schema->getItemsPerPage();
        if ($limit >= 0) $queryCaller->pagination($page, $limit);
        return Instantiator::makeResults(static::COMPONENT, $queryCaller->selectDistinct());
    }
}