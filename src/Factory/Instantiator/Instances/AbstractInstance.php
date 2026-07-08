<?php

namespace Lkt\Factory\Instantiator\Instances;

use Exception;
use Lkt\Connectors\Cache\QueryCache;
use Lkt\Debug\VarDumper;
use Lkt\Factory\Instance\DTO\GroupedData;
use Lkt\Factory\Instance\Enums\RetrieveDataMode;
use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Instance\Traits\ItemWithBooleanDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithColorDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithComposedDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithConcatDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithConstantDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithDateDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithEncryptDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithFileDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithFloatDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithForeignKeyDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithForeignKeysDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithIdentifierValueTrait;
use Lkt\Factory\Instance\Traits\ItemWithIntegerDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithJSONDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithMultipleFloatDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithMultipleIntegerDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithPivotDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithRelatedItemDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithRelatedItemsDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithStringDataTrait;
use Lkt\Factory\Instantiator\Cache\InstanceCache;
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
use Lkt\Factory\Instantiator\ValueObjects\ComponentDatabaseIntegration;
use Lkt\Factory\Instantiator\ValueObjects\MonthlyAccuratePages;
use Lkt\Factory\Schemas\Enums\AccessPolicyEndOfLife;
use Lkt\Factory\Schemas\Enums\RelatedFieldClonePolicy;
use Lkt\Factory\Schemas\Exceptions\InvalidComponentException;
use Lkt\Factory\Schemas\Exceptions\InvalidItemDataAssignException;
use Lkt\Factory\Schemas\Exceptions\InvalidSchemaAppClassException;
use Lkt\Factory\Schemas\Exceptions\MissedMandatoryValueException;
use Lkt\Factory\Schemas\Exceptions\SchemaNotDefinedException;
use Lkt\Factory\Schemas\Fields\AbstractField;
use Lkt\Factory\Schemas\Fields\BooleanField;
use Lkt\Factory\Schemas\Fields\ColorField;
use Lkt\Factory\Schemas\Fields\ConcatField;
use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\EmailField;
use Lkt\Factory\Schemas\Fields\EncryptField;
use Lkt\Factory\Schemas\Fields\FileField;
use Lkt\Factory\Schemas\Fields\FloatField;
use Lkt\Factory\Schemas\Fields\ForeignKeyField;
use Lkt\Factory\Schemas\Fields\ForeignKeysField;
use Lkt\Factory\Schemas\Fields\HTMLField;
use Lkt\Factory\Schemas\Fields\IdField;
use Lkt\Factory\Schemas\Fields\IntegerField;
use Lkt\Factory\Schemas\Fields\JSONField;
use Lkt\Factory\Schemas\Fields\MethodGetterField;
use Lkt\Factory\Schemas\Fields\PivotField;
use Lkt\Factory\Schemas\Fields\PivotLeftIdField;
use Lkt\Factory\Schemas\Fields\PivotPositionField;
use Lkt\Factory\Schemas\Fields\RelatedField;
use Lkt\Factory\Schemas\Fields\RelatedKeysField;
use Lkt\Factory\Schemas\Fields\StringChoiceField;
use Lkt\Factory\Schemas\Fields\StringField;
use Lkt\Factory\Schemas\Fields\UnixTimeStampField;
use Lkt\Factory\Schemas\Fields\ValueListField;
use Lkt\Factory\Schemas\Schema;
use Lkt\Factory\Schemas\ValueObjects\AccessPolicy;
use Lkt\Factory\Schemas\ValueObjects\AccessPolicyUsage;
use Lkt\FileBrowser\Enums\FileEntityType;
use Lkt\Instances\LktFileEntity;
use Lkt\Locale\Locale;
use Lkt\QueryBuilding\Query;
use Lkt\QueryBuilding\SelectBuilder;
use Lkt\QueryBuilding\Where;
use Lkt\Translations\Translations;
use function Lkt\Tools\Pagination\getTotalPages;
use function Lkt\Tools\Parse\clearInput;

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
        ItemWithConcatDataTrait;

    use ItemWithIdentifierValueTrait,
        ItemWithDataTrait;

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

    protected array $DATA = [];
    protected array $UPDATED = [];
    protected array $UPLOADING_FILES = [];
    protected array $PIVOT = [];
    protected array $PIVOT_DATA = [];
    protected array $UPDATED_PIVOT_DATA = [];
    protected array $RELATED_DATA = [];
    protected array $UPDATED_RELATED_DATA = [];
    protected array $PENDING_UPDATE_RELATED_DATA = [];
    protected array $PAGES = [];
    protected array $PAGES_TOTAL = [];

    const COMPONENT = '';

    protected array $DECRYPT = [];
    protected array $DECRYPT_UPDATED = [];

    protected AccessPolicyUsage|null $accessPolicy = null;

    /**
     * @param array $initialData
     */
    public function __construct(array $initialData = [])
    {
//        $this->DATA = $initialData;
        $this->initialFeed($initialData);
    }

    public function initialFeed(array $initialData = [], bool $refreshing = false): static
    {
        $schema = Schema::get(static::COMPONENT);
        $groupedData = new GroupedData($schema, $initialData);
//        VarDumper::dump(['initialFeed', static::COMPONENT, $initialData, $groupedData]);

        $this
            ->initStringData($schema, $this, $groupedData->stringData)
            ->initBooleanData($schema, $this, $groupedData->booleanData)
            ->initIntegerData($schema, $this, $groupedData->integerData)
            ->initMultipleIntegerData($schema, $this, $groupedData->multipleIntegerData)
            ->initFloatData($schema, $this, $groupedData->floatData)
            ->initMultipleFloatData($schema, $this, $groupedData->multipleFloatData)
            ->initDateData($schema, $this, $groupedData->dateData)
            ->initColorData($schema, $this, $groupedData->colorData)
            ->initEncryptData($schema, $this, $groupedData->encryptData)
            ->initForeignKeyData($schema, $this, $groupedData->foreignKeyData)
            ->initForeignKeysData($schema, $this, $groupedData->foreignKeysData)
            ->initRelatedItemData($schema, $this, $groupedData->relatedItemData)
            ->initRelatedItemsData($schema, $this, $groupedData->relatedItemsData)
            ->initJSONData($schema, $this, $groupedData->jsonData)
            ->initFileData($schema, $this, $groupedData->fileData)

            ->initPivotData($schema, $this, $refreshing)
            ->initComposedData($schema, $this, $refreshing)

            ->initConstantData($schema, $this)
            ->initConcatData($schema, $this)
        ;

        return $this;
    }

    public function setAccessPolicy(string|AccessPolicy $accessPolicy, AccessPolicyEndOfLife $accessPolicyEndOfLife = AccessPolicyEndOfLife::UntilUpdated): static
    {
        if ($accessPolicy instanceof AccessPolicy) {
            $this->accessPolicy = new AccessPolicyUsage(static::COMPONENT, $accessPolicy->name, $accessPolicyEndOfLife);
        } else {
            $schema = Schema::get(static::COMPONENT);
            $isAnonymous = $this->isAnonymous();
            if ($accessPolicyEndOfLife === AccessPolicyEndOfLife::UntilNextWrite) {
                $modifier = $isAnonymous ? 'mk' : 'up';
                if ($schema->hasAccessPolicy("{$modifier}:$accessPolicy")) {
                    $accessPolicy = "{$modifier}:$accessPolicy";

                } elseif ($schema->hasAccessPolicy("w:$accessPolicy")) {
                    $accessPolicy = "w:$accessPolicy";
                }
            } elseif ($accessPolicyEndOfLife === AccessPolicyEndOfLife::UntilNextRead) {
                if ($schema->hasAccessPolicy("r:$accessPolicy")) {
                    $accessPolicy = "r:$accessPolicy";
                }
            }

            $this->accessPolicy = new AccessPolicyUsage(static::COMPONENT, $accessPolicy, $accessPolicyEndOfLife);
        }
        return $this;
    }

    public function setData(array $initialData): static
    {
        $schema = Schema::get(static::COMPONENT);

        foreach ($initialData as $column => $datum) {
            $field = $schema->getField($column);
            if ($field && $field->hasDefaultValue()) {
                $initialData[$column] = $field->ensureDefaultValue($initialData[$column]);
            }
        }

        $this->initialFeed($initialData);
//        $this->DATA = $initialData;
        return $this;
    }

    public function getSchema(): Schema|null
    {
        return Schema::get(static::COMPONENT);
    }

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

//        $code = Instantiator::getInstanceCode($component, $codeId);

        if (InstanceCache::inCache($code)) {
            $cached = InstanceCache::load($code);
            $cached->hydrate([]); // Could be removed? Maybe the state shouldn't be cleared
            return $cached;
        }

        if (count($initialData) > 0) {
            $r = new static($initialData);
//            $r->initialFeed($initialData); // @todo remove (redundant)
            InstanceCache::store($code, $r);
            return InstanceCache::load($code);
        }

        $dbIntegration = ComponentDatabaseIntegration::from(static::COMPONENT);
        $builder = $dbIntegration->query;
        $schema = $dbIntegration->schema;

        $schema->applyIdentifierConstraintsToQueryFromData($builder, $id);

        $data = $builder->selectDistinct();
        if (count($data) > 0) {
            // @todo remove this data parser
//            $converter = new RawResultsToInstanceConverter(static::COMPONENT, $data[0]);
//            $itemData = $converter->parse();

            $r = new static($data[0]);
//            $r->initialFeed($data);
//            VarDumper::die($data, static::class, $r);
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
     * @deprecated
     * @return mixed
     * @throws InvalidComponentException
     * @throws SchemaNotDefinedException
     */
    public function getIdColumnValue(): mixed
    {

        $data = $this->getOriginalData();
        $schema = Schema::get(static::COMPONENT);
        $identifiers = $schema->getIdentifiers();
        if (count($identifiers) === 1) {
            $identifierValue = $this->getIdentifierValue();
            return $identifierValue[array_keys($identifierValue)[0]];
            return $data[$schema->getIdString()];
        }

        $r = [];
        foreach ($identifiers as $identifier) {
            $k = $identifier->getName();
            $r[$k] = $data[$k];
        }

        return $r;
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
     * Name can be confused due to similarity with feed
     * @todo Should be renamed to something like clearUpdatedData
     *
     * @param array $data
     * @return $this
     */
    public function hydrate(array $data): static
    {
        if (count($data) === 0) {
            $this->UPDATED = [];
            return $this;
        }
        foreach ($data as $column => $datum) $this->UPDATED[$column] = $datum;
        return $this;
    }

    public function delete(): static
    {
        if ($this->isAnonymous()) return $this;

        $dbIntegration = ComponentDatabaseIntegration::from(static::COMPONENT);
        $caller = $dbIntegration->query;
        $connection = $dbIntegration->databaseConnector;
        $connector = $dbIntegration->databaseConnectorName;
        $schema = $dbIntegration->schema;

        $schema->applyIdentifierConstraintsToQueryFromInstance($caller, $this);

        $connection->query($connection->getDeleteQuery($caller));

        foreach ($schema->getFieldsWithAppendForeignKeysName() as $relatedField) {
            $getter = $relatedField->getGetterForData();
            /** @var AbstractInstance[] $relatedElements */
            $relatedElements = $this->{$getter}();
            $relatedSchema = Schema::get($relatedField->getComponent());
            $relatedElementsField = $relatedSchema->getField($relatedField->getColumn());
            foreach ($relatedElements as $element) {
                $element->_removeForeignListIds($relatedElementsField->getName(), [$id])->save();
            }
        }

        $cacheCode = $schema->getInstanceCode($this->getOriginalData());
        InstanceCache::clearCode($cacheCode);
        $query = $connection->getSelectQuery($caller);
        QueryCache::set($connector, $query, []);
        $this->DATA = [];
        $this->UPDATED = [];
        $this->RELATED_DATA = [];
        $this->PIVOT = [];
        $this->PIVOT_DATA = [];
        $this->PIVOT_SORT = [];
        $this->UPDATED_RELATED_DATA = [];
        $this->PENDING_UPDATE_RELATED_DATA = [];
        return $this;
    }

    /**
     * @deprecated
     * @return Query
     * @throws SchemaNotDefinedException
     */
    public static function getQueryCaller()
    {
        return QueryBuilderHelper::getComponentQuery(static::COMPONENT);
//        $dbIntegration = ComponentDatabaseIntegration::from(static::COMPONENT);
//        return $dbIntegration->query;
    }

    /**
     * @return Query
     * @throws SchemaNotDefinedException
     */
    public static function getQueryBuilder()
    {
        return QueryBuilderHelper::getComponentQuery(static::COMPONENT);
//        $dbIntegration = ComponentDatabaseIntegration::from(static::COMPONENT);
//        return $dbIntegration->query;
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

    protected function hasPageLoaded(string $fieldName, int $page): bool
    {
        return isset($this->PAGES[$fieldName][$page])
            && is_array($this->PAGES[$fieldName][$page]);
    }

    protected function hasPageTotal(string $fieldName): bool
    {
        return isset($this->PAGES_TOTAL[$fieldName]);
    }

    protected function prepareCrudData(array $data, CrudOperation|null $operation = null): array
    {
        return $data;
    }

    protected function patchReadData(array $data): array
    {
        return $data;
    }

    public function autoRead(array $internalMethodsArguments = []): array
    {
        $schema = Schema::get(static::COMPONENT);
        if (isset($this->accessPolicy)) {
            $fields = $schema->getAccessPolicyFields($this->accessPolicy);
            $composedFields = $schema->getAccessPolicyComposedFields($this->accessPolicy);

        } else {
            $fields = $schema->getAllFields();
            $composedFields = $schema->getComposedFields();
        }

        $fieldsStack = [...$fields, ...$composedFields];

        $r = $this->patchReadData($this->readFields($fieldsStack, $internalMethodsArguments));

        if (isset($this->accessPolicy) && $this->accessPolicy->matchedEndOfLife(AccessPolicyEndOfLife::UntilNextRead)) {
            unset($this->accessPolicy);
        }

        return $r;
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

        $schema = Schema::get(static::COMPONENT);

        $accessPolicy = null;

        if (isset($instance->accessPolicy) && $instance->accessPolicy) {
            $accessPolicy = $schema->getAccessPolicy($instance->accessPolicy->name);
        }
        /** @var PivotField[] $pivotFields */
        $pivotFields = $schema->getPivotFields();

        $composedInstances = [];

        foreach ($params as $param => $value) {

            $isPivotDatumFeed = false;

            if ($accessPolicy) {

                if (!$accessPolicy?->includesFieldName($param) && !$accessPolicy?->includesCompositionFieldName($param)) {
                    foreach ($pivotFields as $pivotField) {
                        $pivotSchema = $pivotField->getPivotSchema();
                        if ($pivotSchema->hasField($param)) {
                            $isPivotDatumFeed = true;
                            $field = $pivotField;
                        }
                    }
                    if (!$isPivotDatumFeed) continue;
                } else {

                    $field = $accessPolicy->getSchemaField($schema, $param);
                    if (!$field) $field = $accessPolicy->getSchemaCompositionField($schema, $param);
                }

            } else {
                $field = $schema->getField($param);
                if (!$field) $field = $schema->getCompositionFieldComposingThisField($param);
            }

            if (!$field) {
                foreach ($pivotFields as $pivotField) {
                    $pivotSchema = $pivotField->getPivotSchema();
                    if ($pivotSchema->hasField($param)) {
                        $isPivotDatumFeed = true;
                        $field = $pivotField;
                    }
                }
            }

            if (!$field || $field instanceof MethodGetterField || $field instanceof ConcatField) continue;

            $composedDatum = !$schema->hasFieldDefined($param) && !$isPivotDatumFeed;

            // Composed related data
            if ($composedDatum) {

                $composedKey = $param;
                $l = strlen($param);
                $endsWithId = substr($param, $l - 2, 2) === 'Id';
                if ($endsWithId) {
                    $composedKey = substr($param, 0, $l - 2);
                }

                if (!$composedInstances[$composedKey]) {
//                    if ($field instanceof RelatedField || $field instanceof ForeignKeyField) {
//                        /** @var AbstractInstance $composedInstance */
//                        $composedInstance = $instance->_getCompositionInstance($field->getName(), $internalMethodsArguments);
//                    } else {
                        $fieldComposingThisField = $schema->getCompositionFieldComposingThisField($param);
                        if (!$fieldComposingThisField) continue;
                        /** @var AbstractInstance $composedInstance */
                        $composedInstance = $instance->_getCompositionInstance($fieldComposingThisField?->getName(), $internalMethodsArguments);
//                    }
                    $composedInstances[$composedKey] = $composedInstance;
                }


                if ($endsWithId) {
                    $composedInstances[$composedKey]::feedInstance($composedInstance, [
                        "{$composedKey}Id" => $value,
                    ], $internalMethodsArguments);

                } else {
                    $composedInstances[$composedKey]::feedInstance($composedInstance, [
                        $composedKey=> $value,
                    ], $internalMethodsArguments);
                }

                continue;
            }

            // Common primitive value fields (included composed elements thanks to generated setter detection  approach)
            $setter = $field->getSetterForPrimitiveValue();

            if ($field instanceof RelatedField) {
                $setter = '_setRelatedValWithData';
                $methodCallData = ['type' => '', 'column' => $field->getName(), 'data' => $value];
                if ($field->isSingleMode()) {
                    $methodCallData['data'] = [$methodCallData['data']];
                }

            } elseif ($field instanceof ForeignKeyField) {
                if ($field->keyIsId($param)) {
                    $setter = '_setIntegerVal';
                    $methodCallData = ['fieldName' => $field->getName() . 'Id', 'value' => $value];
                }
                elseif (is_numeric($value)) {
                    $setter = '_setIntegerVal';
                    $methodCallData = ['fieldName' => $field->getName(), 'value' => (int)$value];
                } elseif (is_array($value)) {
                    $relatedSchema = Schema::get($field->getComponent($schema, $instance));
                    $relatedIdFields = $relatedSchema->getIdentifiers();
                    if (count($relatedIdFields) === 1) {
                        $relatedIdKey = $relatedIdFields[0]->getName();
                        $relatedId = isset($value[$relatedIdKey]) ? (int)$value[$relatedIdKey] : 0;
                        if ($relatedId > 0) {
                            $setter = '_setIntegerVal';
                            $methodCallData = ['fieldName' => $field->getName(), 'value' => $relatedId];
                        }
                    }
                } else {
                    continue;
                }

            } elseif ($field instanceof ForeignKeysField) {
                if ($field->keyIsIds($param)) {
                    $setter = '_setForeignListVal';
                    $methodCallData = ['fieldName' => $field->getName(), 'value' => $value];

                } elseif (is_array($value) && isset($value[0]) && is_numeric($value[0])) {
                    $setter = '_setForeignListVal';
                    $methodCallData = ['fieldName' => $field->getName(), 'value' => $value];

                } else {
                    $setter = '_setForeignListWithData';
                    $methodCallData = ['fieldName' => $field->getName(), 'data' => $value];
                }

            } elseif ($field instanceof RelatedKeysField) {
                if ($param === $field->getAppendForeignKeysName() && ((is_numeric($value) && $value > 0) || is_array($value))) {
                    $setter = '_appendToParentForeignKeys';
                    $methodCallData = ['field' => $field->getName(), 'parentValue' => $value];
                } else {
                    continue;
                }

            } elseif ($field instanceof PivotField) {
                if ($isPivotDatumFeed) {
                    $setter = '_setPendingPivotLink';
                    $methodCallData = ['field' => $field->getName(), 'relatedId' => (int)$value];
                } else {
                    $setter = '_setPivotSort';
                    $methodCallData = ['column' => $field->getName(), 'data' => $value];
                }

            } else if ($field instanceof StringField || $field instanceof HTMLField) {
                if ($field->isI18nJson() && is_array($value)) {
                    $translationField = $schema->getField("{$field->getName()}Data");
                    if ($translationField) {
                        $setter = '_setJsonVal';
                        $methodCallData = ['fieldName' => $translationField->getName(), 'value' => $value];
                    }

                } else {
                    $methodCallData = [$field->getName() => clearInput($value)];
                }

            } elseif ($field instanceof IntegerField && !$field instanceof IdField && !$field->isMultiple()) {
                $methodCallData = [$field->getName() => (int)$value];

            } elseif ($field instanceof FloatField) {
                $methodCallData = [$field->getName() => (float)$value];

            } else {
                $methodCallData = [$field->getName() => $value];
            }

            if (!is_array($methodCallData)) {
                continue;
            }

            $methodCallData = [...$internalMethodsArguments, ...$methodCallData];

            $methodCallData = $instance->prepareOwnMethodCallArguments($setter, $methodCallData, $field->getName());
            if (!$instance->satisfiedOwnMethodCallArguments($setter, $methodCallData)) {
                continue;
            }
            $instance->callOwnMethod($setter, $methodCallData);
        }

//        foreach ($composedInstances as $param => $instance) {
//            $instance->save();
//        }

        return $instance;
    }

    /**
     * @param AbstractField[] $fields
     * @return array
     */
    public function readFields(array $fields = [], array $internalMethodsArguments = []): array
    {
//        $recursiveReadController = RecursiveReadController::getInstance();

        $accessPolicyName = isset($this->accessPolicy) ? $this->accessPolicy->name : '';

//        if (!$recursiveReadController->log(static::COMPONENT, $accessPolicyName, $this->getIdColumnValue())) {
//            return [];
//        }

        $schema = Schema::get(static::COMPONENT);
        $r = [];
        foreach ($fields as $key => $field) {
            $responseKey = $key ?? $field->getName();
//            if (isset($this->accessPolicy)) {
//                $accessPolicy = $schema->getAccessPolicy($this->accessPolicy->name);
//                $responseKeyAux = $accessPolicy->getFieldPublicName($field);
//                if ($responseKeyAux) {
//                    $responseKey = $responseKeyAux;
//                }
//            }

            $composedDatum = !$schema->hasFieldDefined($field->getName());

            // Composed related data
            if ($composedDatum) {

                $fieldComposingThisField = $schema->getCompositionFieldComposingThisField($field->getName());
                if (!$fieldComposingThisField) continue;

                if ($fieldComposingThisField instanceof RelatedField) {
                    $composedInstance = $this->relatedItemData->getItem($fieldComposingThisField->getName(), $internalMethodsArguments);
                } elseif ($fieldComposingThisField instanceof ForeignKeyField) {
                    $composedInstance = $this->foreignKeyData->getItem($fieldComposingThisField->getName());
                }

                if (!$composedInstance) continue;
                $dataToAdd = $composedInstance->readValue($field->getName(), $responseKey);
                foreach ($dataToAdd as $z => $y) $r[$z] = $y;
                continue;
            }

            $dataToAdd = $this->readValue($field->getName(), $responseKey);
            foreach ($dataToAdd as $z => $y) $r[$z] = $y;
            continue;


            if ($field instanceof RelatedField) {
                $additionalData = $internalMethodsArguments;
                $relatedSchema = Schema::get($field->getComponent());

                if ($relatedSchema->hasComplexPrimaryKey()) {
                    $relatedFieldPointingToMe = $relatedSchema->getField($field->getColumn());

                    if ($relatedFieldPointingToMe) {
                        $additionalData[$relatedFieldPointingToMe->getName()] = $this->getIdColumnValue();
                    }
                }

                $getter = $field->getGetterForPrimitiveValue();

                $additionalData = $this->prepareOwnMethodCallArguments($getter, $additionalData, $field->getName());

                if ($this->satisfiedOwnMethodCallArguments($getter, $additionalData)) {
                    $items = $this->callOwnMethod($getter, $additionalData);
                } else {
                    continue;
                }

                $relatedAccessPolicy = null;
                if (isset($this->accessPolicy)) {
                    $relatedAccessPolicy = $schema->getAccessPolicyForRelationalField($this->accessPolicy, $field);
                }

                if (!$relatedAccessPolicy && Schema::get($field->getComponent())->hasRelatedAccessPolicy()) {
                    $relatedAccessPolicy = 'lkt-related';
                }

                if ($field->isSingleMode()) {
                    if (is_object($items)) {
                        if ($relatedAccessPolicy) $items->setAccessPolicy($relatedAccessPolicy, AccessPolicyEndOfLife::UntilNextRead);
                        $r[$responseKey] = $items->readAsRelated();

                    } elseif ($field->hasToReturnsEmptyOneInSingleMode()) {
                        $anonymous = Instantiator::make($field->getComponent(), 0);
                        if ($relatedAccessPolicy) $anonymous->setAccessPolicy($relatedAccessPolicy, AccessPolicyEndOfLife::UntilNextRead);
                        $r[$responseKey] = $anonymous->readAsRelated();
                    }

                } else {
//                    $t = [];
                    $helperInstance = $relatedSchema->getItemInstance();
                    $batchActions = $helperInstance::getBatchActions($items);
//                    foreach ($items as $item) {
//                        if ($relatedAccessPolicy) $item->setAccessPolicy($relatedAccessPolicy, AccessPolicyEndOfLife::UntilNextRead);
//                        $t[] = $item->readAsRelated();
//                    }
                    $r[$responseKey] = $batchActions->read($relatedAccessPolicy);
                }

            } elseif ($field instanceof ForeignKeysField) {
                $getter = $field->getGetterForData();
                $getterIds = $field->getGetterForPrimitiveValue();
                $items = $this->{$getter}();
                if (!is_array($items)) $items = [];
                $t = [];

                $relatedAccessPolicy = null;
                if (isset($this->accessPolicy)) {
                    $relatedAccessPolicy = $schema->getAccessPolicyForRelationalField($this->accessPolicy, $field);
                }

                if (!$relatedAccessPolicy && $field->getComponent() && Schema::get($field->getComponent())->hasRelatedAccessPolicy()) {
                    $relatedAccessPolicy = 'lkt-related';
                }

                foreach ($items as $item) {
                    if ($relatedAccessPolicy) $item->setAccessPolicy($relatedAccessPolicy, AccessPolicyEndOfLife::UntilNextRead);
                    $t[] = $item->readAsRelated();
                }
                $r[$responseKey] = $t;
                $r[$responseKey . 'Ids'] = $this->{$getterIds}();

            } elseif ($field instanceof ForeignKeyField) {
                $getter = $field->getGetterForData();
                $getterIds = $field->getGetterForPrimitiveValue();
                $item = $this->{$getter}();

                $relatedAccessPolicy = null;
                if (isset($this->accessPolicy)) {
                    $relatedAccessPolicy = $schema->getAccessPolicyForRelationalField($this->accessPolicy, $field);

                }

                if (!$relatedAccessPolicy && Schema::get($field->getComponent($schema, $this))->hasRelatedAccessPolicy()) {
                    $relatedAccessPolicy = 'lkt-related';
                }

                if ($item instanceof AbstractInstance) {
                    if ($relatedAccessPolicy) $item->setAccessPolicy($relatedAccessPolicy, AccessPolicyEndOfLife::UntilNextRead);
                    $item = $item->autoRead();
                }
                if (!is_array($item)) $item = [];
                $r[$responseKey] = $item;
                if (method_exists($this, $getterIds)) {
                    $r[$responseKey . 'Id'] = $this->{$getterIds}();
                }

                if ($field->hasOnReadIncludeOptions()) {
                    $r[$responseKey . 'Opts'] = [$item];
                }

            } elseif ($field instanceof RelatedKeysField) {
                $getter = $field->getGetterForData();
                $getterIds = $field->getGetterForPrimitiveValue();
                $items = $this->{$getter}();
                if (!is_array($items)) $items = [];
                $t = [];

                $relatedAccessPolicy = null;
                if (isset($this->accessPolicy)) {
                    $relatedAccessPolicy = $schema->getAccessPolicyForRelationalField($this->accessPolicy, $field);
                }

                if (!$relatedAccessPolicy && $field->getComponent() && Schema::get($field->getComponent())->hasRelatedAccessPolicy()) {
                    $relatedAccessPolicy = 'lkt-related';
                }

//                if ($key === $field->getAppendForeignKeysName()) {
//                    $t = array_map(function (AbstractInstance $item) { return $item->getIdColumnValue();}, $items);
//
//                } else {
//                    $relatedSchema = Schema::get($field->getComponent());
//                    $helperInstance = $relatedSchema->getItemInstance();
//                    $batchActions = $helperInstance::getBatchActions($items);
//                    $t = $batchActions->read($relatedAccessPolicy, 'related');
//                }

                foreach ($items as $item) {
                    if ($relatedAccessPolicy) $item->setAccessPolicy($relatedAccessPolicy, AccessPolicyEndOfLife::UntilNextRead);

                    if ($key === $field->getAppendForeignKeysName()) {
                        $t[] = $item->getIdColumnValue();
                    } else {
                        $t[] = $item->readAsRelated();
                    }
                }
                $r[$responseKey] = $t;
                if ($key !== $field->getAppendForeignKeysName()) {
                    $r[$responseKey . 'Ids'] = $this->{$getterIds}();
                }

            } elseif ($field instanceof MethodGetterField) {
                $getter = $field->getName();

                $key = $responseKey;
                if (!$key) $key = $field->getColumn();

                $r[$key] = $this->{$getter}();

            } elseif ($field instanceof FileField) {
                $val = '';
                if (!$this->isAnonymous()) {
                    $getter = $field->getGetterForPrimitiveValue().'PublicPath';
                    $val = $this->{$getter}();
                }
                $r[$responseKey] = $val;

            } elseif ($field instanceof DateTimeField || $field instanceof UnixTimeStampField) {
                $getter = $field->getGetterForPrimitiveValue();

                $format = $field->getLangDefaultReadFormat(Locale::getLangCode());
                if (!$format) $format = $field->getDefaultReadFormat();

                if ($format !== '') {
                    $r[$responseKey] = $this->dateData->format($field->getName(), $format);
//                    $r[$responseKey] = $this->{$getter . 'Formatted'}($format);

                } else {
                    $r[$responseKey] = $this->dateData->get($field->getName());
//                    $r[$responseKey] = $this->{$getter}();
                }

            } elseif ($field instanceof PivotField) {

                $getter = $field->getGetterForPrimitiveValue();
                /** @var static[] $items */
                $items = $this->{$getter}();
                if (!is_array($items)) $items = [];
                $t = [];

                $relatedAccessPolicy = null;
                if (isset($this->accessPolicy)) {
                    $relatedAccessPolicy = $schema->getAccessPolicyForRelationalField($this->accessPolicy, $field);

                }

                if (!$relatedAccessPolicy && Schema::get($field->getComponent())->hasRelatedAccessPolicy()) {
                    $relatedAccessPolicy = 'lkt-related';
                }

                foreach ($items as $item) {
                    if ($relatedAccessPolicy) $item->setAccessPolicy($relatedAccessPolicy, AccessPolicyEndOfLife::UntilNextRead);
                    $t[] = $item->readAsRelated();

                }
                $r[$responseKey] = $t;

            } elseif ($field instanceof ValueListField) {
                $getter = $field->getGetterForPrimitiveValue();

                if ($field->readModeIsBoth()) {
                    $r[$responseKey] = $this->{$getter}();
                    $r[$responseKey.'List'] = $this->{$getter.'AsArray'}();

                } elseif ($field->readModeIsString()) {
                    $r[$responseKey] = $this->{$getter}();

                } elseif ($field->readModeIsArray()) {
                    $r[$responseKey] = $this->{$getter.'AsArray'}();
                }

            } elseif ($field instanceof StringChoiceField) {
                $getter = $field->getGetterForPrimitiveValue();
                $value = $this->{$getter}();
                $r[$responseKey] = $value;
                $i18nOptions = $field->getI18nViewOptions();
                if ($i18nOptions !== '') {;
                    $r[$responseKey . 'Text'] = Translations::get($i18nOptions . ".{$value}", Locale::getLangCode());
                }

            } elseif ($field instanceof AbstractField) {

                $additionalData = $internalMethodsArguments;

                $getter = $field->getGetterForPrimitiveValue();

                $additionalData = $this->prepareOwnMethodCallArguments($getter, $additionalData, $field->getName());

                if ($this->satisfiedOwnMethodCallArguments($getter, $additionalData)) {
                    $r[$responseKey] = $this->callOwnMethod($getter, $additionalData);
                }
            }
        }

//        RecursiveReadController::endStack(static::COMPONENT, $accessPolicyName, $this->getIdColumnValue());

        if (method_exists($this, 'postProcessRead')) return $this->postProcessRead($r);
        ksort($r);
        return $r;
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

    public function linkPivot(string $pivotComponent, $id): static
    {
        $pivotSchema = Schema::get($pivotComponent);

        $pointingField = $pivotSchema->getOneFieldPointingToComponent(static::COMPONENT);

        if ($pointingField instanceof PivotLeftIdField) {
            $referencedField = $pivotSchema->getPivotRightIdField();
        } else {
            $referencedField = $pivotSchema->getPivotLeftIdField();
        }

        /** @var PivotPositionField $positionField */
        $positionField = $pivotSchema->getOnePositionField();

        $pivotQueryBuilder = QueryBuilderHelper::getComponentQuery($pivotComponent);

        $pivotQueryBuilder->setColumns(["MAX({$positionField->getColumn()}) AS {$positionField->getName()}"]);

        $results = $pivotQueryBuilder->select();
        $nextPosition = $results[0]['position'] === null ? 0 : (int)$results[0]['position'] + 1;


        $instance = $pivotSchema->getItemInstance();

        $pointingSetter = $pointingField->getSetterForPrimitiveValue();
        $instance->{$pointingSetter}($this->getIdColumnValue());

        $referencedSetter = $referencedField->getSetterForPrimitiveValue();
        $instance->{$referencedSetter}($id);

        $positionSetter = $positionField->getSetterForPrimitiveValue();
        $instance->{$positionSetter}($nextPosition);

        $instance->save();
        return $this;
    }

    public function unlinkPivot(string $pivotComponent, $id): static
    {
        $pivotSchema = Schema::get($pivotComponent);

        $pointingField = $pivotSchema->getOneFieldPointingToComponent(static::COMPONENT);

        if ($pointingField instanceof PivotLeftIdField) {
            $referencedField = $pivotSchema->getPivotRightIdField();
        } else {
            $referencedField = $pivotSchema->getPivotLeftIdField();
        }

        $pivotQueryBuilder = QueryBuilderHelper::getComponentQuery($pivotComponent);

        $pointingGetter = $pointingField->getGetterForPrimitiveValue();
        $pivotQueryBuilder->andIntegerEqual($pointingField->getColumn(), $this->{$pointingGetter}());

        $referencedGetter = $referencedField->getGetterForPrimitiveValue();
        $pivotQueryBuilder->andIntegerEqual($referencedField->getColumn(), $this->{$referencedGetter}());

        $anonymous = $pivotSchema->getItemInstance();
        $instance = $anonymous::getOne($pivotQueryBuilder);
        $instance->delete();
        return $this;
    }

    public function duplicate(): static
    {
        $clone = static::getInstance()->setAccessPolicy('duplicate');
        $data = $this->autoRead();
        $payload = [];
        $schema = Schema::get(static::COMPONENT);
        $includeDuplicatedTextInField = $schema->getIncludeDuplicatedTextInField();

        foreach ($data as $fieldName => $value) {
            $field = $schema->getField($fieldName);
            if ($field->getName() === $includeDuplicatedTextInField?->getName()) {
                if ($includeDuplicatedTextInField instanceof JSONField) {

                    $temp = [];
                    foreach (Locale::getAvailableLangCodes() as $langCode) {
                        $suffix = Translations::get('adminHelper.duplicatedText', $langCode->value);
                        $temp[$langCode->value] = "{$value[$langCode->value]} {$suffix}";
                    }

                    $payload[$fieldName] = $temp;

                } else {
                    $suffix = Translations::get('adminHelper.duplicatedText');
                    $payload[$fieldName] = "{$value} {$suffix}";
                }

            } else {

                $addToPayload = true;
                $addToPayloadValue = $value;

                if ($field instanceof ForeignKeyField) {
                    $clonePolicy = $field->getRelatedFieldClonePolicy();
                    switch ($clonePolicy) {
                        case RelatedFieldClonePolicy::KeepReferences:
                            $addToPayload = true;
                            break;

                        case RelatedFieldClonePolicy::Ignore:
                            $addToPayload = false;
                            break;
                    }

                } elseif ($field instanceof ForeignKeysField) {
                    $clonePolicy = $field->getRelatedFieldClonePolicy();
                    switch ($clonePolicy) {
                        case RelatedFieldClonePolicy::KeepReferences:
                            $addToPayload = true;
                            break;

                        case RelatedFieldClonePolicy::Ignore:
                            $addToPayload = false;
                            break;
                    }

                } elseif ($field instanceof RelatedKeysField) {
                    $clonePolicy = $field->getRelatedFieldClonePolicy();
                    switch ($clonePolicy) {
                        case RelatedFieldClonePolicy::KeepReferences:

                            $appendForeignKeysName = $field->getAppendForeignKeysName();
                            $appendForeignKeysVal = [];
                            if ($appendForeignKeysName) {
                                foreach ($data[$fieldName] as $datum) {
                                    if (is_numeric($datum)) {
                                        $appendForeignKeysVal[] = $datum;

                                    } else {
                                        $appendForeignKeysVal[] = $datum['id'];
                                    }
                                }
                            }

                            $payload[$fieldName] = $appendForeignKeysVal;
                            $addToPayload = false;
                            break;

                        case RelatedFieldClonePolicy::Ignore:
                            $addToPayload = false;
                            break;
                    }
                }

                if ($addToPayload) {
                    $payload[$fieldName] = $addToPayloadValue;
                }
            }
        }

        $payload = $clone->prepareCrudData($payload, CrudOperation::Create);

        static::feedInstance($clone, $payload);
        return $clone;
    }

    public function saveDuplicate(): static
    {
        return $this->duplicate()->save();
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

    public static function getBatchActions(array $items): BatchActions
    {
        return BatchActions::fromComponent(static::COMPONENT, $items);
    }

    public function getUpdatePayload(): array
    {
        $r = [];
        foreach ($this->UPDATED as $k => $v) $r[$k] = $v;

        foreach ($this->stringData->getPayload() as $k => $v) $r[$k] = $v;
        foreach ($this->integerData->getPayload() as $k => $v) $r[$k] = $v;
        foreach ($this->multipleIntegerData->getPayload() as $k => $v) $r[$k] = $v;
        foreach ($this->floatData->getPayload() as $k => $v) $r[$k] = $v;
        foreach ($this->multipleFloatData->getPayload() as $k => $v) $r[$k] = $v;
        foreach ($this->booleanData->getPayload() as $k => $v) $r[$k] = $v;
        foreach ($this->dateData->getPayload() as $k => $v) $r[$k] = $v;
        foreach ($this->colorData->getPayload() as $k => $v) $r[$k] = $v;
        foreach ($this->encryptData->getPayload() as $k => $v) $r[$k] = $v;
        foreach ($this->foreignKeyData->getPayload() as $k => $v) $r[$k] = $v;
        foreach ($this->foreignKeysData->getPayload() as $k => $v) $r[$k] = $v;
        foreach ($this->jsonData->getPayload() as $k => $v) $r[$k] = $v;
        foreach ($this->fileData->getPayload() as $k => $v) $r[$k] = $v;

        return $r;
    }

    public function getOriginalData(): array
    {
        $r = [];
        foreach ($this->UPDATED as $k => $v) $r[$k] = $v; // @todo remove this line

        foreach ($this->stringData->getOriginalData() as $k => $v) $r[$k] = $v;
        foreach ($this->integerData->getOriginalData() as $k => $v) $r[$k] = $v;
        foreach ($this->multipleIntegerData->getOriginalData() as $k => $v) $r[$k] = $v;
        foreach ($this->floatData->getOriginalData() as $k => $v) $r[$k] = $v;
        foreach ($this->multipleFloatData->getOriginalData() as $k => $v) $r[$k] = $v;
        foreach ($this->booleanData->getOriginalData() as $k => $v) $r[$k] = $v;
        foreach ($this->dateData->getOriginalData() as $k => $v) $r[$k] = $v;
        foreach ($this->colorData->getOriginalData() as $k => $v) $r[$k] = $v;
        foreach ($this->encryptData->getOriginalData() as $k => $v) $r[$k] = $v;
        foreach ($this->foreignKeyData->getOriginalData() as $k => $v) $r[$k] = $v;
        foreach ($this->foreignKeysData->getOriginalData() as $k => $v) $r[$k] = $v;
        foreach ($this->jsonData->getOriginalData() as $k => $v) $r[$k] = $v;
        foreach ($this->fileData->getOriginalData() as $k => $v) $r[$k] = $v;

        return $r;
    }

    public function getAccessPolicyUsage(): ?AccessPolicyUsage
    {
        return $this->accessPolicy;
    }

    /**
     * This method overrides all the $this->{$setter}() dynamic assigns
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     * @throws InvalidComponentException
     * @throws InvalidItemDataAssignException
     * @throws SchemaNotDefinedException
     * @throws \Lkt\Factory\Schemas\Exceptions\DuplicatedValueException
     */
    public function assignValue(string $key, mixed $value): static
    {
        $field = $this->getSchema()->getField($key);
        if (!$field) throw InvalidItemDataAssignException::missingField($key);

        if ($field instanceof StringField || $field instanceof EmailField) {
            $this->stringData->set($key, $value);

        } elseif ($field instanceof IntegerField) {
            if ($field->isMultiple()) {
                $this->multipleIntegerData->set($key, $value);
            } else {
                $this->integerData->set($key, $value);
            }

        } elseif ($field instanceof FloatField) {
            if ($field->isMultiple()) {
                $this->multipleFloatData->set($key, $value);
            } else {
                $this->floatData->set($key, $value);
            }

        } elseif ($field instanceof BooleanField) {
            $this->booleanData->set($key, $value);

        } elseif ($field instanceof DateTimeField || $field instanceof UnixTimeStampField) {
            $this->dateData->set($key, $value);

        } elseif ($field instanceof JSONField) {
            $this->jsonData->set($key, $value);

        } elseif ($field instanceof EncryptField) {
            $this->encryptData->set($key, $value);

        } elseif ($field instanceof ColorField) {
            $this->colorData->set($key, $value);

        } elseif ($field instanceof FileField) {
            $this->fileData->set($key, $value);

        } elseif ($field instanceof ForeignKeyField) {
            $this->foreignKeyData->set($key, $value);

        } elseif ($field instanceof ForeignKeysField) {
            $this->foreignKeysData->set($key, $value);

        } elseif ($field instanceof RelatedField) {
            $this->relatedItemsData->setItems($key, (array)$value);

        } elseif ($field instanceof PivotField) {
            $this->pivotData->setItems($key, (array)$value);
        }

        return $this;
    }


    public function retrieveValue(string $key, array $additionalData = [], RetrieveDataMode $dataMode = RetrieveDataMode::Raw): mixed
    {
        $field = $this->getSchema()->getField($key);
        if (!$field) throw InvalidItemDataAssignException::missingField($key);

        if ($field instanceof StringField || $field instanceof EmailField) {
            return $this->stringData->get($key);

        } elseif ($field instanceof FloatField) {
            if ($field->isMultiple()) {
                return $this->multipleFloatData->get($key);
            } else {
                return $this->floatData->get($key);
            }

        } elseif ($field instanceof BooleanField) {
            return $this->booleanData->get($key);

        } elseif ($field instanceof DateTimeField || $field instanceof UnixTimeStampField) {
            return $this->dateData->get($key);

        } elseif ($field instanceof JSONField) {
            return $this->jsonData->get($key);

        } elseif ($field instanceof EncryptField) {
            return $this->encryptData->get($key);

        } elseif ($field instanceof ColorField) {
            return $this->colorData->get($key);

        } elseif ($field instanceof FileField) {
            if ($dataMode === RetrieveDataMode::Raw) {
                return $this->fileData->get($key);
            }
            return $this->fileData->getPublicPath($key);

        } elseif ($field instanceof ForeignKeyField) {
            if ($dataMode === RetrieveDataMode::Raw) {
                return $this->foreignKeyData->get($key);
            }
            return $this->foreignKeyData->getItem($key, $additionalData);

        } elseif ($field instanceof ForeignKeysField) {
            if ($dataMode === RetrieveDataMode::Raw) {
                return $this->foreignKeysData->get($key);
            }
            return $this->foreignKeysData->getItems($key);

        } elseif ($field instanceof RelatedField) {
            if ($field->isSingleMode()) return $this->relatedItemData->getItem($key, $additionalData);
            return $this->relatedItemsData->getItems($key);

        } elseif ($field instanceof RelatedKeysField) {
            return $this->relatedItemsData->getItems($key);

        } elseif ($field instanceof ConcatField) {
            return $this->concatData->get($key);

        } elseif ($field instanceof PivotField) {
            return $this->pivotData->getItems($key);

        } elseif ($field instanceof IntegerField) {
            if ($field->isMultiple()) {
                return $this->multipleIntegerData->get($key);
            } else {
                return $this->integerData->get($key);
            }
        }

        return null;
    }

    public function hasAssignedValue(string $key, array $additionalData = []): bool
    {
        $field = $this->getSchema()->getField($key);
        if (!$field) throw InvalidItemDataAssignException::missingField($key);

        if ($field instanceof StringField || $field instanceof EmailField) {
            return $this->stringData->has($key);

        } elseif ($field instanceof FloatField) {
            if ($field->isMultiple()) {
                return $this->multipleFloatData->has($key);
            } else {
                return $this->floatData->has($key);
            }

        } elseif ($field instanceof BooleanField) {
            return $this->booleanData->has($key);

        } elseif ($field instanceof DateTimeField || $field instanceof UnixTimeStampField) {
            return $this->dateData->has($key);

        } elseif ($field instanceof JSONField) {
            return $this->jsonData->has($key);

        } elseif ($field instanceof EncryptField) {
            return $this->encryptData->has($key);

        } elseif ($field instanceof ColorField) {
            return $this->colorData->has($key);

        } elseif ($field instanceof FileField) {
            return $this->fileData->has($key);

        } elseif ($field instanceof ForeignKeyField) {
            return $this->foreignKeyData->has($key);

        } elseif ($field instanceof ForeignKeysField) {
            return $this->foreignKeysData->has($key);

        } elseif ($field instanceof RelatedField) {
            if ($field->isSingleMode()) return $this->relatedItemData->has($key);
            return $this->relatedItemsData->has($key);

        } elseif ($field instanceof RelatedKeysField) {
            return $this->relatedItemsData->has($key);

        } elseif ($field instanceof ConcatField) {
            return $this->concatData->has($key);

        } elseif ($field instanceof PivotField) {
            return $this->pivotData->has($key);

        } elseif ($field instanceof IntegerField) {
            if ($field->isMultiple()) {
                return $this->multipleIntegerData->has($key);
            } else {
                return $this->integerData->has($key);
            }
        }

        return false;
    }

    public function readValue(string $key, string $responseKey, array $additionalData = []): array|null
    {
        $field = $this->getSchema()->getField($key);
        if (!$field) throw InvalidItemDataAssignException::missingField($key);

        if ($field instanceof StringField || $field instanceof EmailField) {
            return [$responseKey => $this->stringData->get($key)];

        } elseif ($field instanceof BooleanField) {
            return [$responseKey => $this->booleanData->get($key)];

        } elseif ($field instanceof DateTimeField || $field instanceof UnixTimeStampField) {
            return [$responseKey => $this->dateData->get($key)];

        } elseif ($field instanceof JSONField) {
            return [$responseKey => $this->jsonData->get($key)];

        } elseif ($field instanceof EncryptField) {
            return [$responseKey => $this->encryptData->get($key)];

        } elseif ($field instanceof ColorField) {
            return [$responseKey => $this->colorData->get($key)];

        } elseif ($field instanceof FileField) {
            return [$responseKey => $this->fileData->getPublicPath($key)];

        } elseif ($field instanceof ForeignKeyField) {

            $relatedAccessPolicy = null;
            $accessPolicyUsage = $this->getAccessPolicyUsage();
            $schema = $this->getSchema();

            if ($accessPolicyUsage) {
                $relatedAccessPolicy = $schema->getAccessPolicyForRelationalField($this->accessPolicy, $field);
            }

            if (!$relatedAccessPolicy && Schema::get($field->getComponent($schema, $this))->hasRelatedAccessPolicy()) {
                $relatedAccessPolicy = 'lkt-related';
            }

            $item = $this->foreignKeyData->getItem($key, $additionalData);
            if ($item instanceof AbstractInstance) {
                if ($relatedAccessPolicy) $item->setAccessPolicy($relatedAccessPolicy, AccessPolicyEndOfLife::UntilNextRead);
                $item = $item->autoRead();
            }

            $r = [];
            if (!is_array($item)) $item = [];
            $r[$responseKey] = $item;
            $r[$responseKey . 'Id'] = $this->foreignKeyData->get($key);
            if ($field->hasOnReadIncludeOptions()) {
                $r[$responseKey . 'Opts'] = [$item];
            }

            return $r;

        } elseif ($field instanceof ForeignKeysField) {

            $relatedAccessPolicy = null;
            $accessPolicyUsage = $this->getAccessPolicyUsage();
            $schema = $this->getSchema();

            if ($accessPolicyUsage) {
                $relatedAccessPolicy = $schema->getAccessPolicyForRelationalField($this->accessPolicy, $field);
            }

            if (!$relatedAccessPolicy && Schema::get($field->getComponent($schema, $this))->hasRelatedAccessPolicy()) {
                $relatedAccessPolicy = 'lkt-related';
            }

            $items = $this->foreignKeysData->getItems($key);
            $r = [];
            if (!is_array($items)) $items = [];
            $t = [];

            foreach ($items as $item) {
                if ($relatedAccessPolicy) $item->setAccessPolicy($relatedAccessPolicy, AccessPolicyEndOfLife::UntilNextRead);
                $t[] = $item->autoRead();
            }
            $r[$responseKey] = $t;
            $r[$responseKey . 'Ids'] = $this->foreignKeysData->getIds($key);


            return $r;

        } elseif ($field instanceof RelatedField) {

            $r = [];
            $relatedSchema = Schema::get($field->getComponent());

            if ($relatedSchema->hasComplexPrimaryKey()) {
                $relatedFieldPointingToMe = $relatedSchema->getField($field->getColumn());

                if ($relatedFieldPointingToMe) {
                    $additionalData[$relatedFieldPointingToMe->getName()] = $this->getIdColumnValue();
                }
            }

            $getter = $field->getGetterForPrimitiveValue();

//            if ($field->isSingleMode()) {
//                $items = $this->relatedItemData->getItem($field->getName(), $additionalData);
//            } else {
//                $items = $this->relatedItemsData->getItems($field->getName());
//            }

            $additionalData = $this->prepareOwnMethodCallArguments($getter, $additionalData, $field->getName());

            // @todo replace callOwnMethod with data retrieve?
            if ($this->satisfiedOwnMethodCallArguments($getter, $additionalData)) {
                $items = $this->callOwnMethod($getter, $additionalData);
            } else {
                return null;
            }

            $relatedAccessPolicy = null;
            $accessPolicyUsage = $this->getAccessPolicyUsage();
            $schema = $this->getSchema();

            if ($accessPolicyUsage) {
                $relatedAccessPolicy = $schema->getAccessPolicyForRelationalField($this->accessPolicy, $field);
            }

            if (!$relatedAccessPolicy && Schema::get($field->getComponent($schema, $this))->hasRelatedAccessPolicy()) {
                $relatedAccessPolicy = 'lkt-related';
            }

            if ($field->isSingleMode()) {
                if (is_object($items)) {
                    if ($relatedAccessPolicy) $items->setAccessPolicy($relatedAccessPolicy, AccessPolicyEndOfLife::UntilNextRead);
                    $r[$responseKey] = $items->autoRead();

                } elseif ($field->hasToReturnsEmptyOneInSingleMode()) {
                    $anonymous = Instantiator::make($field->getComponent(), 0);
                    if ($relatedAccessPolicy) $anonymous->setAccessPolicy($relatedAccessPolicy, AccessPolicyEndOfLife::UntilNextRead);
                    $r[$responseKey] = $anonymous->autoRead();
                }

            } else {
                $helperInstance = $relatedSchema->getItemInstance();
                $batchActions = $helperInstance::getBatchActions($items);
                $r[$responseKey] = $batchActions->read($relatedAccessPolicy);
            }


            return $r;

        } elseif ($field instanceof RelatedKeysField) {

            $r = [];

            $relatedAccessPolicy = null;
            $accessPolicyUsage = $this->getAccessPolicyUsage();
            $schema = $this->getSchema();

            if ($accessPolicyUsage) {
                $relatedAccessPolicy = $schema->getAccessPolicyForRelationalField($this->accessPolicy, $field);
            }

            if (!$relatedAccessPolicy && Schema::get($field->getComponent($schema, $this))->hasRelatedAccessPolicy()) {
                $relatedAccessPolicy = 'lkt-related';
            }


            $items = $this->relatedItemsData->getItems($key);
            if (!is_array($items)) $items = [];
            $t = [];

            foreach ($items as $item) {
                if ($relatedAccessPolicy) $item->setAccessPolicy($relatedAccessPolicy, AccessPolicyEndOfLife::UntilNextRead);

                if ($key === $field->getAppendForeignKeysName()) {
                    $t[] = $item->getIdColumnValue();
                } else {
                    $t[] = $item->readAsRelated();
                }
            }
            $r[$responseKey] = $t;
            if ($key !== $field->getAppendForeignKeysName()) {
                $r[$responseKey . 'Ids'] = $this->relatedItemsData->getItemsIds($key);;
            }


            return $r;

        } elseif ($field instanceof IntegerField) {
            if ($field->isMultiple()) {
                return [$responseKey => $this->multipleIntegerData->get($key)];
            } else {
                return [$responseKey => $this->integerData->get($key)];
            }

        } elseif ($field instanceof FloatField) {
            if ($field->isMultiple()) {
                return [$responseKey => $this->multipleFloatData->get($key)];
            } else {
                return [$responseKey => $this->floatData->get($key)];
            }

        } elseif ($field instanceof MethodGetterField) {
            $getter = $field->getName();
            return [$responseKey => $this->{$getter}()];

        } elseif ($field instanceof PivotField) {

            $schema = $this->getSchema();
            $getter = $field->getGetterForPrimitiveValue();
            /** @var static[] $items */
            $items = $this->{$getter}();
            if (!is_array($items)) $items = [];
            $r = [];
            $t = [];

            $relatedAccessPolicy = null;
            if (isset($this->accessPolicy)) {
                $relatedAccessPolicy = $schema->getAccessPolicyForRelationalField($this->accessPolicy, $field);

            }

            if (!$relatedAccessPolicy && Schema::get($field->getComponent())->hasRelatedAccessPolicy()) {
                $relatedAccessPolicy = 'lkt-related';
            }

            foreach ($items as $item) {
                if ($relatedAccessPolicy) $item->setAccessPolicy($relatedAccessPolicy, AccessPolicyEndOfLife::UntilNextRead);
                $t[] = $item->readAsRelated();

            }
            $r[$responseKey] = $t;
            return $r;

        } elseif ($field instanceof ValueListField) {
            $r = [];
            $getter = $field->getGetterForPrimitiveValue();

            if ($field->readModeIsBoth()) {
                $r[$responseKey] = $this->{$getter}();
                $r[$responseKey.'List'] = $this->{$getter.'AsArray'}();

            } elseif ($field->readModeIsString()) {
                $r[$responseKey] = $this->{$getter}();

            } elseif ($field->readModeIsArray()) {
                $r[$responseKey] = $this->{$getter.'AsArray'}();
            }
            return $r;

        } elseif ($field instanceof StringChoiceField) {
            $r = [];
            $getter = $field->getGetterForPrimitiveValue();
            $value = $this->{$getter}();
            $r[$responseKey] = $value;
            $i18nOptions = $field->getI18nViewOptions();
            if ($i18nOptions !== '') {;
                $r[$responseKey . 'Text'] = Translations::get($i18nOptions . ".{$value}", Locale::getLangCode());
            }
            return $r;

        } elseif ($field instanceof ConcatField) {
            return [$responseKey => $this->concatData->get($key)];
        }

        return null;
    }
}