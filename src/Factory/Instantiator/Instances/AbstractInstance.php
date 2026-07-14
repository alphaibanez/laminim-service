<?php

namespace Lkt\Factory\Instantiator\Instances;

use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Instance\Traits\ItemWithAccessPolicyTrait;
use Lkt\Factory\Instance\Traits\ItemWithBooleanDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithColorDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithComposedDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithConcatDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithConstantDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithCrudTrait;
use Lkt\Factory\Instance\Traits\ItemWithDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithDateDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithEncryptDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithFileDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithFloatDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithForeignKeyDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithForeignKeysDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithIdentifierValueTrait;
use Lkt\Factory\Instance\Traits\ItemWithInstanceFactoryTrait;
use Lkt\Factory\Instance\Traits\ItemWithIntegerDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithJSONDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithMultipleFloatDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithMultipleIntegerDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithMultipleStringDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithPivotDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithRelatedItemDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithRelatedItemsDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithStringDataTrait;
use Lkt\Factory\Instantiator\Conversions\InstanceToArray;
use Lkt\Factory\Instantiator\Enums\CrudOperation;
use Lkt\Factory\Instantiator\Exceptions\InvalidCountableFieldException;
use Lkt\Factory\Instantiator\Exceptions\UnsetFieldStorePathException;
use Lkt\Factory\Instantiator\Helpers\QueryBuilderHelper;
use Lkt\Factory\Instantiator\Instances\AccessDataTraits\ColumnBooleanTrait;
use Lkt\Factory\Instantiator\Instances\AccessDataTraits\ColumnColorTrait;
use Lkt\Factory\Instantiator\Instances\AccessDataTraits\ColumnCompositionTrait;
use Lkt\Factory\Instantiator\Instances\AccessDataTraits\ColumnConcatTrait;
use Lkt\Factory\Instantiator\Instances\AccessDataTraits\ColumnConstantValueTrait;
use Lkt\Factory\Instantiator\Instances\AccessDataTraits\ColumnDateTimeTrait;
use Lkt\Factory\Instantiator\Instances\AccessDataTraits\ColumnEmailTrait;
use Lkt\Factory\Instantiator\Instances\AccessDataTraits\ColumnEncryptTrait;
use Lkt\Factory\Instantiator\Instances\AccessDataTraits\ColumnFileTrait;
use Lkt\Factory\Instantiator\Instances\AccessDataTraits\ColumnFloatTrait;
use Lkt\Factory\Instantiator\Instances\AccessDataTraits\ColumnForeignListTrait;
use Lkt\Factory\Instantiator\Instances\AccessDataTraits\ColumnForeignTrait;
use Lkt\Factory\Instantiator\Instances\AccessDataTraits\ColumnIntegerChoiceTrait;
use Lkt\Factory\Instantiator\Instances\AccessDataTraits\ColumnIntegerTrait;
use Lkt\Factory\Instantiator\Instances\AccessDataTraits\ColumnJsonTrait;
use Lkt\Factory\Instantiator\Instances\AccessDataTraits\ColumnPivotTrait;
use Lkt\Factory\Instantiator\Instances\AccessDataTraits\ColumnRelatedKeysMergeTrait;
use Lkt\Factory\Instantiator\Instances\AccessDataTraits\ColumnRelatedKeysTrait;
use Lkt\Factory\Instantiator\Instances\AccessDataTraits\ColumnRelatedTrait;
use Lkt\Factory\Instantiator\Instances\AccessDataTraits\ColumnStringChoiceTrait;
use Lkt\Factory\Instantiator\Instances\AccessDataTraits\ColumnStringTrait;
use Lkt\Factory\Instantiator\Instances\AccessDataTraits\ColumnValueListTrait;
use Lkt\Factory\Instantiator\Instantiator;
use Lkt\Factory\Instantiator\ValueObjects\MonthlyAccuratePages;
use Lkt\Factory\Schemas\Enums\AccessPolicyEndOfLife;
use Lkt\Factory\Schemas\Exceptions\InvalidComponentException;
use Lkt\Factory\Schemas\Exceptions\InvalidSchemaAppClassException;
use Lkt\Factory\Schemas\Exceptions\MissedMandatoryValueException;
use Lkt\Factory\Schemas\Exceptions\SchemaNotDefinedException;
use Lkt\Factory\Schemas\Fields\AbstractField;
use Lkt\Factory\Schemas\Fields\StringField;
use Lkt\Factory\Schemas\Schema;
use Lkt\FileBrowser\Enums\FileEntityType;
use Lkt\Instances\LktFileEntity;
use Lkt\Locale\Locale;
use Lkt\QueryBuilding\Query;
use Lkt\QueryBuilding\SelectBuilder;
use Lkt\QueryBuilding\Where;

abstract class AbstractInstance implements Item
{
    use ItemWithStringDataTrait,
        ItemWithIntegerDataTrait,
        ItemWithMultipleIntegerDataTrait,
        ItemWithFloatDataTrait,
        ItemWithMultipleFloatDataTrait,
        ItemWithBooleanDataTrait,
        ItemWithDateDataTrait,
        ItemWithColorDataTrait,
        ItemWithEncryptDataTrait,
        ItemWithForeignKeyDataTrait,
        ItemWithForeignKeysDataTrait,
        ItemWithRelatedItemDataTrait,
        ItemWithRelatedItemsDataTrait,
        ItemWithPivotDataTrait,
        ItemWithComposedDataTrait,
        ItemWithFileDataTrait,
        ItemWithJSONDataTrait,
        ItemWithConstantDataTrait,
        ItemWithConcatDataTrait,
        ItemWithMultipleStringDataTrait;

    use ItemWithIdentifierValueTrait,
        ItemWithDataTrait,
        ItemWithAccessPolicyTrait,
        ItemWithInstanceFactoryTrait,
        ItemWithCrudTrait;

    use ColumnStringTrait,
        ColumnIntegerTrait,
        ColumnFloatTrait,
        ColumnEmailTrait,
        ColumnBooleanTrait,
        ColumnColorTrait,
        ColumnJsonTrait,
        ColumnFileTrait,
        ColumnForeignTrait,
        ColumnForeignListTrait,
        ColumnRelatedTrait,
        ColumnRelatedKeysTrait,
        ColumnPivotTrait,
        ColumnDateTimeTrait,
        ColumnStringChoiceTrait,
        ColumnIntegerChoiceTrait,
        ColumnEncryptTrait,
        ColumnRelatedKeysMergeTrait,
        ColumnValueListTrait,
        ColumnConcatTrait,
        ColumnCompositionTrait,
        ColumnConstantValueTrait;

    protected array $UPDATED_RELATED_DATA = [];
    protected array $PENDING_UPDATE_RELATED_DATA = [];

    const COMPONENT = '';

    /**
     * @param array $initialData
     */
    public function __construct(array $initialData = [])
    {
        $this->initialFeed($initialData);
    }

    public function getSchema(): Schema|null
    {
        return Schema::get(static::COMPONENT);
    }

    /**
     * @throws InvalidComponentException
     * @throws InvalidSchemaAppClassException
     * @throws SchemaNotDefinedException
     * @deprecated
     */
    public function convertToComponent(string $component = ''): ?static
    {
        return Instantiator::make($component, $this->getIdColumnValue());
    }

    /**
     * @deprecated
     * @return Query
     * @throws SchemaNotDefinedException
     */
    public static function getQueryCaller()
    {
        return QueryBuilderHelper::getComponentQuery(static::COMPONENT);
    }

    /**
     * @return Query
     * @throws SchemaNotDefinedException
     */
    public static function getQueryBuilder()
    {
        return QueryBuilderHelper::getComponentQuery(static::COMPONENT);
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

    public function getComponent(): string
    {
        return static::COMPONENT;
    }

    public function toArray(): array
    {
        return InstanceToArray::convert($this);
    }

    protected function prepareCrudData(array $data, CrudOperation|null $operation = null): array
    {
        return $data;
    }

    protected function patchReadData(array $data): array
    {
        return $data;
    }

    /**
     * @deprecated by feedAndSave (which automatically checks is has to create or not)
     *
     * @param array $data
     * @param array $internalMethodsArguments
     * @return $this
     * @throws MissedMandatoryValueException
     * @throws UnsetFieldStorePathException
     */
    public function autoCreate(array $data, array $internalMethodsArguments = []): static
    {
        static::feedInstance($this, $this->prepareCrudData($data, CrudOperation::Create), $internalMethodsArguments);
        return $this->save();
    }

    /**
     * @deprecated by feedAndSave (which automatically checks is has to create or not)
     *
     * @param array $data
     * @param array $internalMethodsArguments
     * @return $this
     * @throws MissedMandatoryValueException
     * @throws UnsetFieldStorePathException
     */
    public function autoUpdate(array $data, array $internalMethodsArguments = []): static
    {
        static::feedInstance($this, $this->prepareCrudData($data, CrudOperation::Update), $internalMethodsArguments);
        return $this->save();
    }

    public static function create(array $params): static
    {
        return (new static())->autoCreate($params);
    }

    public static function update(AbstractInstance $instance, array $params): static
    {
        return $instance->autoUpdate($params);
    }

    public static function feedInstance(AbstractInstance $instance, array $params, array $internalMethodsArguments = []): static
    {
        return $instance->feed($params, $internalMethodsArguments);
    }

    /**
     * @param AbstractField[] $fields
     * @return array
     * @deprecated
     */
    public function readAsRelated(array $internalMethodsArguments = []): array
    {
        $schema = Schema::get(static::COMPONENT);
        if ($this->accessPolicy) {
            $fields = $schema->getAccessPolicyFields($this->accessPolicy);
            $composedFields = $schema->getAccessPolicyComposedFields($this->accessPolicy);

        } else if ($schema->hasRelatedAccessPolicy()) {
            $fields = $schema->getAccessPolicyFields('lkt-related');
            $composedFields = $schema->getAccessPolicyComposedFields('lkt-related');
            $this->setAccessPolicy('lkt-related', AccessPolicyEndOfLife::UntilNextRead);

        } else {
            $fields = $schema->getSameTableFields();
            $composedFields = $schema->getComposedFields();
        }

        $fieldsStack = [...$fields, ...$composedFields];

        $r = $this->patchReadData($this->readFields($fieldsStack, $internalMethodsArguments));

        if (isset($this->accessPolicy) && $this->accessPolicy->matchedEndOfLife(AccessPolicyEndOfLife::UntilNextRead)) {
            unset($this->accessPolicy);
        }

        return $r;
    }


    public static function getUniqueFilteredQueryBuilder(array $data): Query
    {
        $schema = Schema::get(static::COMPONENT);

        $query = static::getQueryCaller();
        $langCodes = Locale::getAvailableLangCodesValues();

        foreach ($schema->getUniqueFields() as $field) {
            if ($field instanceof StringField) {
                if ($field->isI18nJson()) {
                    foreach ($langCodes as $langCode) {
                        if (isset($data[$field->getName()][$langCode])) {
                            $query->andStringEqual($field->getLocaleColumn($langCode), $data[$field->getName()][$langCode]);
                        }
                    }
                } else {
                    $query->andStringEqual($field->getColumn(), $data[$field->getName()]);
                }
            }
        }

        return $query;
    }

    public static function getWhereBuilder(): Where
    {
        return Instantiator::getCustomWhere(static::COMPONENT);
    }

    public static function mkOrUp(array $data): static
    {
        $instance = static::getOne(static::getUniqueFilteredQueryBuilder($data));
        if (!$instance) {
            $instance = static::getInstance()->autoCreate($data);
        } else {
            $instance->autoUpdate($data);
        }
        return $instance;
    }

    public static function mkIfNot(array $data): static
    {
        $instance = static::getOne(static::getUniqueFilteredQueryBuilder($data));
        if (!$instance) {
            $instance = static::getInstance()->autoCreate($data);
        }
        return $instance;
    }

    public static $schemaStorePath = null;
    public static $schemaPublicPath = null;


    public static function getSchemaStorePath($instance): string
    {
        if (is_callable(static::$schemaStorePath)) {
            return call_user_func(static::$schemaStorePath, $instance);
        }
        return '';
    }


    public static function getSchemaPublicPath(LktFileEntity|null $instance = null): string
    {
        if ($instance instanceof LktFileEntity) {
            if ($instance->getType() === FileEntityType::StorageUnit->value || $instance->getType() === FileEntityType::Directory->value) return '';
        }

        if (is_callable(static::$schemaPublicPath)) {
            return call_user_func(static::$schemaPublicPath, $instance);
        }
        return '';
    }
}