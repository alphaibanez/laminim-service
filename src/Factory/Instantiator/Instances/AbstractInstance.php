<?php

namespace Lkt\Factory\Instantiator\Instances;

use Exception;
use Lkt\Connectors\Cache\QueryCache;
use Lkt\Debug\VarDumper;
use Lkt\Factory\Instance\DataControllers\StringDataController;
use Lkt\Factory\Instance\DTO\GroupedData;
use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Instance\Traits\ItemWithBooleanDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithColorDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithConstantDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithDateDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithEmailDataTrait;
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
use Lkt\Factory\Instance\Traits\ItemWithRelatedItemDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithRelatedItemsDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithStringDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithUnixTimestampDataTrait;
use Lkt\Factory\Instantiator\Cache\InstanceCache;
use Lkt\Factory\Instantiator\ComponentId;
use Lkt\Factory\Instantiator\Conversions\InstanceToArray;
use Lkt\Factory\Instantiator\Conversions\RawResultsToInstanceConverter;
use Lkt\Factory\Instantiator\Enums\CrudOperation;
use Lkt\Factory\Instantiator\Exceptions\InvalidCountableFieldException;
use Lkt\Factory\Instantiator\Exceptions\UnsetFieldStorePathException;
use Lkt\Factory\Instantiator\Helpers\FileUploadHelper;
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
use Lkt\Factory\Schemas\Exceptions\InvalidSchemaAppClassException;
use Lkt\Factory\Schemas\Exceptions\MissedMandatoryValueException;
use Lkt\Factory\Schemas\Exceptions\SchemaNotDefinedException;
use Lkt\Factory\Schemas\Fields\AbstractField;
use Lkt\Factory\Schemas\Fields\ConcatField;
use Lkt\Factory\Schemas\Fields\DateTimeField;
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
use function Lkt\Tools\Arrays\compareArrays;
use function Lkt\Tools\Pagination\getTotalPages;
use function Lkt\Tools\Parse\clearInput;

abstract class AbstractInstance implements Item
{
    use ItemWithStringDataTrait,
        ItemWithIntegerDataTrait,
        ItemWithMultipleIntegerDataTrait,
        ItemWithFloatDataTrait,
        ItemWithMultipleFloatDataTrait,
        ItemWithEmailDataTrait,
        ItemWithBooleanDataTrait,
        ItemWithUnixTimestampDataTrait,
        ItemWithDateDataTrait,
        ItemWithColorDataTrait,
        ItemWithEncryptDataTrait,
        ItemWithForeignKeyDataTrait,
        ItemWithForeignKeysDataTrait,
        ItemWithRelatedItemDataTrait,
        ItemWithRelatedItemsDataTrait,
        ItemWithFileDataTrait,
        ItemWithJSONDataTrait,
        ItemWithConstantDataTrait;

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

    public function initialFeed(array $initialData = []): static
    {
//        VarDumper::dump(['initialFeed', static::COMPONENT, $initialData]);
        $schema = Schema::get(static::COMPONENT);
        $groupedData = new GroupedData($schema, $initialData);

        $this
            ->initStringData($schema, $this, $groupedData->stringData)
            ->initEmailData($schema, $this, $groupedData->emailData)
            ->initBooleanData($schema, $this, $groupedData->booleanData)
            ->initIntegerData($schema, $this, $groupedData->integerData)
            ->initMultipleIntegerData($schema, $this, $groupedData->multipleIntegerData)
            ->initFloatData($schema, $this, $groupedData->floatData)
            ->initMultipleFloatData($schema, $this, $groupedData->multipleFloatData)
            ->initUnixTimeStampData($schema, $this, $groupedData->unixTimeStampData)
            ->initDateData($schema, $this, $groupedData->dateData)
            ->initColorData($schema, $this, $groupedData->colorData)
            ->initEncryptData($schema, $this, $groupedData->encryptData)
            ->initForeignKeyData($schema, $this, $groupedData->foreignKeyData)
            ->initForeignKeysData($schema, $this, $groupedData->foreignKeysData)
            ->initRelatedItemData($schema, $this, $groupedData->relatedItemData)
            ->initRelatedItemsData($schema, $this, $groupedData->relatedItemsData)
            ->initJSONData($schema, $this, $groupedData->jsonData)
            ->initFileData($schema, $this, $groupedData->fileData)

            ->initConstantData($schema, $this)
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

    /**
     * @deprecated use getUpdatedPayload instead
     * @return array
     */
    public function getUpdatedData(): array
    {
        return $this->UPDATED;
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
            $r = new static();

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
            $cached->hydrate([]);
            return $cached;
        }

        if (count($initialData) > 0) {
            $r = new static($initialData);
            $r->initialFeed($initialData);
            InstanceCache::store($code, $r);
            return InstanceCache::load($code);
        }

        $dbIntegration = ComponentDatabaseIntegration::from(static::COMPONENT);
        $builder = $dbIntegration->query;
        $schema = $dbIntegration->schema;

        $schema->applyIdentifierConstraintsToQueryFromData($builder, $id);

        $data = $builder->selectDistinct();
        if (count($data) > 0) {
            $converter = new RawResultsToInstanceConverter(static::COMPONENT, $data[0]);
            $itemData = $converter->parse();

            $r = new static($itemData);
            $r->initialFeed($itemData);
            InstanceCache::store($code, $r);
            return InstanceCache::load($code);
        }

        return new static();
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

//    public function save(): static
//    {
//        $isUpdate = !$this->isAnonymous();
//
//        $dbIntegration = ComponentDatabaseIntegration::from(static::COMPONENT);
//        $queryBuilder = $dbIntegration->query;
//        $connection = $dbIntegration->databaseConnector;
//        $schema = $dbIntegration->schema;
//
//        if (isset($this->accessPolicy) && $this->accessPolicy) {
//            $accessPolicyExcludedFields = $schema->getAccessPolicyExcludedFields($this->accessPolicy->name);
//
//            foreach ($accessPolicyExcludedFields as $accessPolicyExcludedField) {
//                $key = $accessPolicyExcludedField->getName();
//                $hasKey = $accessPolicyExcludedField->getGetterForChecker();
//                if (array_key_exists($key, $this->UPDATED)) unset($this->UPDATED[$key]);
//                if (array_key_exists($hasKey, $this->UPDATED)) unset($this->UPDATED[$hasKey]);
//            }
//        }
//
//        // Update only: auto update values
//        if ($isUpdate) {
//            /** @var AbstractField $fieldsWithDefaultValue */
//            $fieldsWithDefaultValue = $schema->getFieldsToUpdateOnInstanceUpdate();
//            foreach ($fieldsWithDefaultValue as $fieldWithDefaultValue) {
//                $defaultValueKey = $fieldWithDefaultValue->getName();
//                if (isset($this->UPDATED[$defaultValueKey])) continue;
//
//                $defaultValueKey = $fieldWithDefaultValue->getName().'Id';
//                if (isset($this->UPDATED[$defaultValueKey])) continue;
//
//                $defaultValue = $fieldWithDefaultValue->getOnInstanceUpdateValue();
//                $setter = $fieldWithDefaultValue->getSetterForPrimitiveValue();
//                $this->{$setter}($defaultValue);
//            }
//
//        // Create only: set default values
//        } else {
//            /** @var AbstractField $fieldsWithDefaultValue */
//            $fieldsWithDefaultValue = $schema->getFieldsWithDefaultValue();
//            foreach ($fieldsWithDefaultValue as $fieldWithDefaultValue) {
//                $defaultValueKey = $fieldWithDefaultValue->getName();
//                if (isset($this->UPDATED[$defaultValueKey])) continue;
//
//                $defaultValueKey = $fieldWithDefaultValue->getName().'Id';
//                if (isset($this->UPDATED[$defaultValueKey])) continue;
//
//                $defaultValue = $fieldWithDefaultValue->getDefaultValue();
//                $setter = $fieldWithDefaultValue->getSetterForPrimitiveValue();
//                $this->{$setter}($defaultValue);
//            }
//        }
//
//
//        $fileFields = $schema->getFileFields();
//
//        $pendingUploadBase64Files = [];
//        $pendingUploadBase64MultipleFiles = [];
//        if (count($this->UPDATED) > 0) {
//            // Check if it's needed to store a base64 file:
//            foreach ($fileFields as $fileField) {
//                if ($this->_fileValUpdatedWithBase64Data($fileField->getName())) {
//                    $storePath = $fileField->getStorePath($this);
//                    if ($storePath === ''){
//                        throw UnsetFieldStorePathException::getInstance($fileField->getName(), $schema->getComponent());
//                    }
//
//                    if ($fileField->isMultiple()) {
//                        $pendingUploadBase64MultipleFiles[$fileField->getName()] = $this->UPDATED[$fileField->getName()];
//                        $this->UPDATED[$fileField->getName()] = [];
//
//                    } else {
//                        $pendingUploadBase64Files[$fileField->getName()] = $this->UPDATED[$fileField->getName()];
//                        $this->UPDATED[$fileField->getName()] = '';
//                    }
//                }
//            }
//        }
//
//        foreach ($schema->getMandatoryFields() as $mandatoryField) {
//            $checkerMethod = $mandatoryField->getGetterForChecker();
//            if (!$this->{$checkerMethod}()) {
//                $additionalFieldsToColumn =  array_filter($schema->getFields(), function (AbstractField $field) use ($mandatoryField) {
//                    return $field->getName() !== $mandatoryField->getName()
//                        && $field->getColumn() === $mandatoryField->getColumn();
//                });
//                $ok = false;
//                if (count($additionalFieldsToColumn) > 0) {
//                    foreach ($additionalFieldsToColumn as $additionalFieldToColumn) {
//                        $additionalCheckerMethod = $additionalFieldToColumn->getGetterForChecker();
//                        $ok = $ok || $this->{$additionalCheckerMethod}();
//                    }
//                }
//
//                if (!$ok) {
//                    throw MissedMandatoryValueException::getInstance($schema->getComponent() . '.' .$mandatoryField->getName());
//                }
//            }
//        }
//
//        $parsed = $connection->prepareDataToStore($schema, $this->UPDATED);
//
//        $origIdColumn = $schema->getIdColumn();
//        $origIdColumn = $origIdColumn[0];
//
//        $id = 0;
//
//        if (count($this->UPDATED) > 0) {
//
//            // Save current instance process
//            $queryBuilder->updateData($parsed);
//
//            if ($isUpdate) {
//                $schema->applyIdentifierConstraintsToQueryFromInstance($queryBuilder, $this);
//                $query = $connection->getUpdateQuery($queryBuilder);
//            } else {
//                $query = $connection->getInsertQuery($queryBuilder);
//            }
//
//            $queryResponse = $connection->query($query);
//
//            if ($queryResponse !== false) {
//                foreach ($this->UPDATED as $k => $v) {
//                    $this->DATA[$k] = $v;
//                    unset($this->UPDATED[$k]);
//                }
//            }
//
//            $id = (int)$connection->getLastInsertedId();
//            $reload = true;
//        }
//
//        // Get current instance ID (if it's been created)
//        if ($id > 0 && (!isset($this->DATA[$origIdColumn]) || !$this->DATA[$origIdColumn])) {
//            $this->DATA[$origIdColumn] = $id;
//
//        } elseif ($this->DATA[$origIdColumn] > 0) {
//            $id = $this->DATA[$origIdColumn];
//        }
//
//        $hasToReUpdate = false;
//
//        if (count($pendingUploadBase64Files) > 0) {
//            foreach ($pendingUploadBase64Files as $fileFieldName => $fileFieldValue) {
//                $this->_storeBase64DataAsFile($fileFieldName, $fileFieldValue, $id);
//                $hasToReUpdate = true;
//            }
//        }
//
//        if (count($pendingUploadBase64MultipleFiles) > 0) {
//            foreach ($pendingUploadBase64MultipleFiles as $fileFieldName => $fileFieldValue) {
//                $this->_storeBase64DataAsFiles($fileFieldName, $fileFieldValue, $id);
//                $hasToReUpdate = true;
//            }
//        }
//
//        if (count($this->UPLOADING_FILES) > 0) {
//            // Check if it's needed to store a base64 file:
//            foreach ($fileFields as $fileField) {
//                $key = $fileField->getName();
//                if (is_array($this->UPLOADING_FILES[$key])) {
//                    $uploadData = FileUploadHelper::uploadFileField($fileField, $this->UPLOADING_FILES[$key], $this, $schema);
//
//                    if (is_array($uploadData)) {
//                        $this->_setFileVal($key, $uploadData['name']);
//                        $hasToReUpdate = true;
//                    }
//                    unset($this->UPLOADING_FILES[$key]);
//                }
//            }
//        }
//
//        // Update relational data
//        if (count($this->PENDING_UPDATE_RELATED_DATA) > 0) {
//            foreach ($this->PENDING_UPDATE_RELATED_DATA as $column => $data) {
//
//                if (!$isUpdate && count($data) === 0) continue;
//
//                /** @var RelatedField $field */
//                $field = $schema->getField($column);
//                $relatedComponent = $field->getComponent();
//
//                if ($field instanceof ForeignKeysField && count($data) === 0) {
//                    $currentItems = $this->_getForeignListData($column);
//                    if (count($currentItems) === 0) continue;
//                }
//
//                if (method_exists($field, 'getDynamicComponentField')) { // Check due to RelatedField not implementing this feature yet
//                    $dynamicComponentFieldName = $field->getDynamicComponentField();
//                    if ($dynamicComponentFieldName !== '') {
//                        $dynamicComponentField = $schema->getField($dynamicComponentFieldName);
//                        $getter = $dynamicComponentField->getGetterForPrimitiveValue();
//                        $dynamicType = $this->{$getter}();
//                        if (is_numeric($dynamicType)) $relatedComponent = ComponentId::getComponent((int)$dynamicType);
//                        elseif ($dynamicType !== '') $relatedComponent = $dynamicType;
//                    }
//                }
//
//                $relatedSchema = Schema::get($relatedComponent);
//
//                $relatedIdColumn = $relatedSchema->getIdColumn()[0];
//                /** @var AbstractInstance $relatedClass */
//                $relatedClass = $relatedSchema->getInstanceSettings()->getAppClass();
//
//                $relatedMode = false;
//                $foreignKeysMode = false;
//
//                $foreignKeysIds = [];
//
//                // Check which items must be deleted
//                if ($field instanceof RelatedField) {
//                    $relatedMode = true;
//                    $currentItems = $this->_getRelatedVal($relatedComponent, $column, true);
//
//                } elseif ($field instanceof ForeignKeysField) {
//                    $foreignKeysMode = true;
//                    $currentItems = $this->_getForeignListData($column);
//                }
//
//                $currentIds = [];
//
//                /** @var AbstractInstance[] $currentItems */
//                foreach ($currentItems as $currentItem) {
//                    $code = $relatedSchema->getInstanceCode($currentItem);
//                    if (!in_array($code, $currentIds, true)) $currentIds[] = $code;
//                }
//
//                $updatedIds = [];
//                foreach ($data as $datum) {
//                    $code = $relatedSchema->getInstanceCode($datum);
//                    if ($code) $updatedIds[] = $code;
//                }
//
//                $diff = compareArrays($currentIds, $updatedIds);
//
//                // Delete
//                if (method_exists($field, 'hasToAutoRemoveUnlinked') && $field->hasToAutoRemoveUnlinked()) {
//                    foreach ($diff['deleted'] as $deletedId) {
//                        $ins = $relatedClass::getInstance($relatedSchema->decodeInstanceCode($deletedId));
//                        $ins->delete();
//                    }
//                }
//
//                if ($relatedMode) {
//                    $relatedForeignKeyColumn = $relatedSchema->getField($field->getColumn());
//                    $relatedForeignKeyKey = $relatedForeignKeyColumn->getName();
//                    if ($relatedForeignKeyColumn instanceof ForeignKeyField) {
//                        if (!$relatedForeignKeyColumn->keyIsId($relatedForeignKeyKey)) {
//                            $relatedForeignKeyKey .= 'Id';
//                        }
//                    }
//
//                } elseif ($foreignKeysMode) {
//
//                    $relatedForeignKeyColumn = $relatedSchema->getField($field->getColumn());
//                    if ($relatedForeignKeyColumn instanceof RelatedKeysField) {
//                        $relatedForeignKeyKey = $relatedForeignKeyColumn?->getAppendForeignKeysName();
//                        if (!$relatedForeignKeyKey) $relatedForeignKeyKey = $relatedForeignKeyColumn->getName();
//
//                        if ($relatedForeignKeyColumn instanceof ForeignKeyField) {
//                            if (!$relatedForeignKeyColumn->keyIsId($relatedForeignKeyKey)) {
//                                $relatedForeignKeyKey .= 'Id';
//                            }
//                        }
//                    }
//                }
//
//                if (isset($this->accessPolicy) && $this->accessPolicy->name) {
//                    $relatedAccessPolicy = $field->getAssociatedAccessPolicy($this->accessPolicy->name);
//                }
//                if (!$relatedAccessPolicy) $relatedAccessPolicy = 'lkt-related';
//
//
//                // Update or create
//                foreach ($data as $datum) {
//                    if ($relatedMode && $relatedForeignKeyKey && !$datum[$relatedForeignKeyKey]) {
//                        $datum[$relatedForeignKeyKey] = $this->getIdColumnValue();
//                    }
//                    elseif ($foreignKeysMode && $relatedForeignKeyKey && !$datum[$relatedForeignKeyKey]) {
//                        $datum[$relatedForeignKeyKey] = $this->getIdColumnValue();
//                    }
//
//                    if ($datum[$relatedIdColumn] > 0) {
//                        $ins = $relatedClass::getInstance($datum[$relatedIdColumn]);
//                        if ($relatedAccessPolicy) $ins->setAccessPolicy($relatedAccessPolicy, AccessPolicyEndOfLife::UntilNextWrite);
//                        $ins->autoUpdate($datum);
//
//                    } else {
//                        $ins = $relatedClass::getInstance();
//                        if ($relatedAccessPolicy) $ins->setAccessPolicy($relatedAccessPolicy, AccessPolicyEndOfLife::UntilNextWrite);
//                        $ins->autoCreate($datum);
//                    }
//
//                    if ($foreignKeysMode) $foreignKeysIds[] = $ins->getId();
//                }
//
//                if ($foreignKeysMode && count($foreignKeysIds) > 0) {
//                    $setter = 'set' . ucfirst($field->getName());
//                    $this->{$setter}($foreignKeysIds);
//                    $hasToReUpdate = true;
//                }
//            }
//            $this->PENDING_UPDATE_RELATED_DATA = [];
//        }
//
//        if ($hasToReUpdate) {
//            $this->save();
//        }
//
//        if (count($this->PIVOT_SORT) > 0) {
//            foreach ($this->PIVOT_SORT as $column => $ids) {
//
//                $ownField = $schema->getPivotField($column);
//
//                // Pivot table fields (intermediate table)
//                $pivotSchema = $ownField->getPivotSchema();
//
//                $pointingField = $pivotSchema->getOneFieldPointingToComponent(static::COMPONENT);
//
//                if ($pointingField instanceof PivotLeftIdField) {
//                    $referencedField = $pivotSchema->getPivotRightIdField();
//                } else {
//                    $referencedField = $pivotSchema->getPivotLeftIdField();
//                }
//
//                /** @var PivotPositionField $positionField */
//                $positionField = $pivotSchema->getOnePositionField();
//
//                $positionGetter = $positionField->getGetterForPrimitiveValue();
//                $positionSetter = $positionField->getSetterForPrimitiveValue();
//                $referencedGetter = $referencedField->getGetterForPrimitiveValue();
//                $referencedSetter = $referencedField->getSetterForPrimitiveValue();
//                $pointingSetter = $pointingField->getSetterForPrimitiveValue();
//
//                $results = $this->_getPivots($ownField->getName());
//
//                $checkedIds = [];
//
//                // Update existing pivots
//                foreach ($results as $result) {
//                    $id = $result->{$referencedGetter}();
//                    $updatedPosition = array_search($id, $ids);
//                    $checkedIds[] = $id;
//
//                    $position = $result->{$positionGetter}();
//
//                    if ($updatedPosition !== $position) {
//                        $result
//                            ->{$positionSetter}($updatedPosition)
//                            ->save();
//                    }
//
//                    // Unlink pivot relation
//                    if (!in_array($id, $ids, true)) {
//                        $result->delete();
//                    }
//                }
//
//                // Link new pivot relations
//                foreach ($ids as $i => $id) {
//                    if (!in_array($id, $checkedIds, true)) {
//                        $ins = $pivotSchema->getItemInstance();
//                        $ins
//                            ->{$pointingSetter}($this->getIdColumnValue())
//                            ->{$referencedSetter}($id)
//                            ->{$positionSetter}($i)
//                            ->save();
//                    }
//                }
//            }
//        }
//
//        if (count($this->PENDING_PIVOT_LINKS) > 0) {
//            foreach ($this->PENDING_PIVOT_LINKS as $field => $relatedId) {
//                $this->_addPivotRelation($field, $relatedId);
//            }
//        }
//
//        if (count($this->PENDING_PARENT_FOREIGN_KEYS) > 0) {
//            foreach ($this->PENDING_PARENT_FOREIGN_KEYS as $field => $relatedId) {
//                if (is_array($relatedId)) {
//                    foreach ($relatedId as $value) {
//                        if ($value !== null) $this->_saveAppendToParentForeignKeys($field, $value);
//                    }
//                } else {
//                    $this->_saveAppendToParentForeignKeys($field, $relatedId);
//                }
//            }
//        }
//
//        $this->_saveCompositionValues($isUpdate);
//
//        if (isset($this->accessPolicy) && $this->accessPolicy->matchedEndOfLife(AccessPolicyEndOfLife::UntilNextWrite)) {
//            unset($this->accessPolicy);
//        }
//
//        if ($reload) {
//            $cacheCode = $schema->getInstanceCode($this->DATA);
//            InstanceCache::clearCode($cacheCode);
//            return Instantiator::make(static::COMPONENT, $this->getIdColumnValue(), $this->DATA);
//        }
//
//        return $this;
//    }

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
                    $r[$responseKey] = $this->{$getter . 'Formatted'}($format);

                } else {
                    $r[$responseKey] = $this->{$getter}();
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
        foreach ($this->emailData->getPayload() as $k => $v) $r[$k] = $v;
        foreach ($this->dateData->getPayload() as $k => $v) $r[$k] = $v;
        foreach ($this->unixTimeStampData->getPayload() as $k => $v) $r[$k] = $v;
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
        foreach ($this->UPDATED as $k => $v) $r[$k] = $v;

        foreach ($this->stringData->getOriginalData() as $k => $v) $r[$k] = $v;
        foreach ($this->integerData->getOriginalData() as $k => $v) $r[$k] = $v;
        foreach ($this->multipleIntegerData->getOriginalData() as $k => $v) $r[$k] = $v;
        foreach ($this->floatData->getOriginalData() as $k => $v) $r[$k] = $v;
        foreach ($this->multipleFloatData->getOriginalData() as $k => $v) $r[$k] = $v;
        foreach ($this->booleanData->getOriginalData() as $k => $v) $r[$k] = $v;
        foreach ($this->emailData->getOriginalData() as $k => $v) $r[$k] = $v;
        foreach ($this->dateData->getOriginalData() as $k => $v) $r[$k] = $v;
        foreach ($this->unixTimeStampData->getOriginalData() as $k => $v) $r[$k] = $v;
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
}