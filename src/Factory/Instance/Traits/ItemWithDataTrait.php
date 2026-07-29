<?php

namespace Lkt\Factory\Instance\Traits;

use Lkt\Debug\VarDumper;
use Lkt\Factory\Instance\DTO\GroupedData;
use Lkt\Factory\Instance\Enums\RetrieveDataMode;
use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Instantiator\Cache\InstanceCache;
use Lkt\Factory\Instantiator\Enums\CrudOperation;
use Lkt\Factory\Instantiator\Exceptions\UnsetFieldStorePathException;
use Lkt\Factory\Instantiator\Instances\AbstractInstance;
use Lkt\Factory\Instantiator\Instantiator;
use Lkt\Factory\Instantiator\ValueObjects\ComponentDatabaseIntegration;
use Lkt\Factory\Schemas\Enums\AccessPolicyEndOfLife;
use Lkt\Factory\Schemas\Exceptions\InvalidComponentException;
use Lkt\Factory\Schemas\Exceptions\InvalidItemDataAssignException;
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
use Lkt\Factory\Schemas\Fields\IntegerField;
use Lkt\Factory\Schemas\Fields\JSONField;
use Lkt\Factory\Schemas\Fields\MethodGetterField;
use Lkt\Factory\Schemas\Fields\PivotField;
use Lkt\Factory\Schemas\Fields\RelatedField;
use Lkt\Factory\Schemas\Fields\RelatedKeysField;
use Lkt\Factory\Schemas\Fields\StringChoiceField;
use Lkt\Factory\Schemas\Fields\StringField;
use Lkt\Factory\Schemas\Fields\UnixTimeStampField;
use Lkt\Factory\Schemas\Fields\ValueListField;
use Lkt\Factory\Schemas\Schema;
use Lkt\Locale\Locale;
use Lkt\Translations\Translations;
use function Lkt\Tools\Arrays\compareArrays;

trait ItemWithDataTrait
{
    public function initialFeed(array $initialData = [], bool $refreshing = false): static
    {
        $schema = $this->getSchema();
        $groupedData = new GroupedData($schema, $initialData);

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
            ->initJSONData($schema, $this, $groupedData->jsonData)
            ->initFileData($schema, $this, $groupedData->fileData)
            ->initMultipleStringData($schema, $this, $groupedData->multipleStringData)

            ->initRelatedItemsData($schema, $this, $groupedData->relatedItemsData, $refreshing)
            ->initPivotData($schema, $this, $refreshing)
            ->initComposedData($schema, $this, $refreshing)

            ->initConstantData($schema, $this)
            ->initConcatData($schema, $this)
        ;

        return $this;
    }

    /** @todo puede que se pueda parametrizar initialFeed para que acepte un parámetro que agregue los valores por defecto */
    public function setData(array $initialData): static
    {
        $schema = $this->getSchema();

        foreach ($initialData as $column => $datum) {
            $field = $schema->getField($column);
            if ($field && $field->hasDefaultValue()) {
                $initialData[$column] = $field->ensureDefaultValue($initialData[$column]);
            }
        }

        $this->initialFeed($initialData);
        return $this;
    }

    public function feed(array $data, array $internalMethodsArguments = []): static
    {
        $schema = $this->getSchema();
        $accessPolicyUsage = $this->getAccessPolicyUsage();
        $accessPolicy = null;

        if ($accessPolicyUsage) {
            $accessPolicy = $schema->getAccessPolicy($accessPolicyUsage->name);
        }

        $pivotFields = $schema->getPivotFields();

        $composedInstances = [];

        foreach ($data as $param => $value) {

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

            // Pivot table data linking
            if ($isPivotDatumFeed) {
                $this->pivotData->prepareToLink($field->getName(), $value);
                continue;
            }

            // Composed related data
            $composedDatum = !$schema->hasFieldDefined($param);
            if ($composedDatum) {

                $composedKey = $param;
                $l = strlen($param);
                $endsWithId = substr($param, $l - 2, 2) === 'Id';
                if ($endsWithId) {
                    $composedKey = substr($param, 0, $l - 2);
                }

                if (!$composedInstances[$composedKey]) {
                    $fieldComposingThisField = $schema->getCompositionFieldComposingThisField($param);
                    if (!$fieldComposingThisField) continue;
                    /** @var Item $composedInstance */
                    $composedInstance = $this->_getCompositionInstance($fieldComposingThisField?->getName(), $internalMethodsArguments);
                    $composedInstances[$composedKey] = $composedInstance;
                }

                $composedInstances[$composedKey]->feed([
                    $composedKey => $value,
                ], $internalMethodsArguments);

                continue;
            }

            // Common primitive value fields (included composed elements thanks to generated setter detection  approach)
            $key = $field instanceof RelatedKeysField && $param === $field->getAppendForeignKeysName() ? $param : $field->getName();
            $this->assignValue($key, $value);
        }

        if (count($composedInstances) > 0) {
            $this->composedData->setItems($composedInstances);
        }

        return $this;
    }

    public function feedAndSave(array $data, array $internalMethodsArguments = []): static
    {
        $operation = $this->isAnonymous() ? CrudOperation::Create : CrudOperation::Update;
        return $this->feed($this->prepareCrudData($data, $operation), $internalMethodsArguments)->save();
    }


    public function save(): static
    {
        $isUpdate = !$this->isAnonymous();

        $schema = $this->getSchema();
        $dbIntegration = ComponentDatabaseIntegration::from($schema->getComponent());
        $queryBuilder = $dbIntegration->query;
        $connection = $dbIntegration->databaseConnector;
        $schema = $dbIntegration->schema;

        // Update foreign keys before payload calc
        // In order to update inner field
        if (isset($this->foreignKeysData) && $this->foreignKeysData->hasToSave()) {
            $this->foreignKeysData->save();
        }

        // Assign default values
        /** @var AbstractField $fieldsWithDefaultValue */
        $fieldsWithDefaultValue = $isUpdate ? $schema->getFieldsToUpdateOnInstanceUpdate() : $schema->getFieldsWithDefaultValue();
        foreach ($fieldsWithDefaultValue as $fieldWithDefaultValue) {
            $defaultValueKey = $fieldWithDefaultValue->getName();
            if ($this->hasAssignedValue($defaultValueKey)) continue;

            $defaultValue = $isUpdate ? $fieldWithDefaultValue->getOnInstanceUpdateValue() : $fieldWithDefaultValue->getDefaultValue();
            $this->assignValue($defaultValueKey, $defaultValue);
        }

        $original = $this->getOriginalData();
        $payload = $this->getUpdatePayload();

        $accessPolicyUsage = $this->getAccessPolicyUsage();

        if ($accessPolicyUsage) {
            $accessPolicyExcludedFields = $schema->getAccessPolicyExcludedFields($accessPolicyUsage->name);

            foreach ($accessPolicyExcludedFields as $accessPolicyExcludedField) {
                $key = $accessPolicyExcludedField->getName();
                $hasKey = $accessPolicyExcludedField->getGetterForChecker();
                if (array_key_exists($key, $payload)) unset($payload[$key]);
                if (array_key_exists($hasKey, $payload)) unset($payload[$hasKey]);
            }
        }


        $fileFields = $schema->getFileFields();

        $pendingUploadBase64Files = [];
        $pendingUploadBase64MultipleFiles = [];
        if (count($payload) > 0) {
            // Check if it's needed to store a base64 file:
            foreach ($fileFields as $fileField) {
                if ($this->fileData->updatedWithBase64String($fileField->getName())) {
                    $storePath = $fileField->getStorePath($this);
                    if ($storePath === ''){
                        throw UnsetFieldStorePathException::getInstance($fileField->getName(), $schema->getComponent());
                    }

                    if ($fileField->isMultiple()) {
                        $pendingUploadBase64MultipleFiles[$fileField->getName()] = $payload[$fileField->getName()];
                        $payload[$fileField->getName()] = [];

                    } else {
                        $pendingUploadBase64Files[$fileField->getName()] = $payload[$fileField->getName()];
                        $payload[$fileField->getName()] = '';
                    }
                }
            }
        }

        foreach ($schema->getMandatoryFields() as $mandatoryField) {
            $checkerMethod = $mandatoryField->getGetterForChecker();
            if (!$this->{$checkerMethod}()) {
                $additionalFieldsToColumn =  array_filter($schema->getFields(), function (AbstractField $field) use ($mandatoryField) {
                    return $field->getName() !== $mandatoryField->getName()
                        && $field->getColumn() === $mandatoryField->getColumn();
                });
                $ok = false;
                if (count($additionalFieldsToColumn) > 0) {
                    foreach ($additionalFieldsToColumn as $additionalFieldToColumn) {
                        $additionalCheckerMethod = $additionalFieldToColumn->getGetterForChecker();
                        $ok = $ok || $this->{$additionalCheckerMethod}();
                    }
                }

                if (!$ok) {
                    throw MissedMandatoryValueException::getInstance($schema->getComponent() . '.' .$mandatoryField->getName());
                }
            }
        }

        $parsed = $connection->prepareDataToStore($schema, $payload);

        $origIdColumn = $schema->getIdColumn();
        $origIdColumn = $origIdColumn[0];

        if (count($parsed) > 0) {

            // Save current instance process
            $queryBuilder->updateData($parsed);

            if (!$this->isAnonymous()) {
                $schema->applyIdentifierConstraintsToQueryFromInstance($queryBuilder, $this);
                $query = $connection->getUpdateQuery($queryBuilder);
            } else {
                $query = $connection->getInsertQuery($queryBuilder);
            }

//            VarDumper::dump([
//                $schema->getComponent(),
//                $this->getIdColumnValue(),
//                $query,
//            ]);
            $queryResponse = $connection->query($query);

            $id = (int)$connection->getLastInsertedId();
            $reload = true;

            // Get current instance ID (if it's been created)
            if ($id > 0 && (!isset($original[$origIdColumn]) || !$original[$origIdColumn])) {
                $original[$origIdColumn] = $id;
                $updatedData[$origIdColumn] = $id;

            } elseif ($original[$origIdColumn] > 0) {
                $id = $original[$origIdColumn];
            }

            if ($queryResponse !== false) {
                $updatedData = array_merge($original, $payload);
                if ($id > 0) {
                    $updatedData[$origIdColumn] = $id;
                }
                $this->initialFeed($updatedData, true);
            }
        }

        $hasToReUpdate = false;

        if (!$this->isAnonymous()) {
            if (count($pendingUploadBase64Files) > 0) {
                foreach ($pendingUploadBase64Files as $fileFieldName => $fileFieldValue) {
                    $this->fileData->base64ToFile($fileFieldName, $fileFieldValue);
                    $hasToReUpdate = true;
                }
            }

            if (count($pendingUploadBase64MultipleFiles) > 0) {
                foreach ($pendingUploadBase64MultipleFiles as $fileFieldName => $fileFieldValue) {
                    $this->fileData->base64ToFiles($fileFieldName, $fileFieldValue);
                    $hasToReUpdate = true;
                }
            }

            if (isset($this->fileData) && $this->fileData->hasPendingHttpUploads()) {
                foreach ($fileFields as $fileField) {
                    $this->fileData->httpUploadToFile($fileField->getName());
                }
            }

            // Update relational data
            if ($isUpdate && count($this->PENDING_UPDATE_RELATED_DATA) > 0) {
                foreach ($this->PENDING_UPDATE_RELATED_DATA as $column => $data) {

                    if (count($data) === 0) continue;

                    /** @var RelatedField $field */
                    $field = $schema->getField($column);
                    $relatedComponent = $field->getComponent($schema, $this);

                    if ($field instanceof ForeignKeysField && count($data) === 0) {
                        $currentItems = $this->_getForeignListData($column);
                        if (count($currentItems) === 0) continue;
                    }

                    $relatedSchema = Schema::get($relatedComponent);

                    $relatedIdColumn = $relatedSchema->getIdColumn()[0];
                    /** @var AbstractInstance $relatedClass */
                    $relatedClass = $relatedSchema->getInstanceSettings()->getAppClass();

                    $relatedMode = false;
                    $foreignKeysMode = false;

                    $foreignKeysIds = [];

                    // Check which items must be deleted
                    if ($field instanceof RelatedField) {
                        $relatedMode = true;
                        $currentItems = $this->_getRelatedVal($relatedComponent, $column, true);

                    } elseif ($field instanceof ForeignKeysField) {
                        $foreignKeysMode = true;
                        $currentItems = $this->_getForeignListData($column);
                    }

                    $currentIds = [];

                    /** @var AbstractInstance[] $currentItems */
                    foreach ($currentItems as $currentItem) {
                        $code = $relatedSchema->getInstanceCode($currentItem);
                        if (!in_array($code, $currentIds, true)) $currentIds[] = $code;
                    }

                    $updatedIds = [];
                    foreach ($data as $datum) {
                        $code = $relatedSchema->getInstanceCode($datum);
                        if ($code) $updatedIds[] = $code;
                    }

                    $diff = compareArrays($currentIds, $updatedIds);

                    // Delete
                    if (method_exists($field, 'hasToAutoRemoveUnlinked') && $field->hasToAutoRemoveUnlinked()) {
                        foreach ($diff['deleted'] as $deletedId) {
                            $ins = $relatedClass::getInstance($relatedSchema->decodeInstanceCode($deletedId));
                            $ins->delete();
                        }
                    }

                    if ($relatedMode) {
                        $relatedForeignKeyColumn = $relatedSchema->getField($field->getColumn());
                        $relatedForeignKeyKey = $relatedForeignKeyColumn->getName();
                        if ($relatedForeignKeyColumn instanceof ForeignKeyField) {
                            if (!$relatedForeignKeyColumn->keyIsId($relatedForeignKeyKey)) {
                                $relatedForeignKeyKey .= 'Id';
                            }
                        }

                    } elseif ($foreignKeysMode) {

                        $relatedForeignKeyColumn = $relatedSchema->getField($field->getColumn());
                        if ($relatedForeignKeyColumn instanceof RelatedKeysField) {
                            $relatedForeignKeyKey = $relatedForeignKeyColumn?->getAppendForeignKeysName();
                            if (!$relatedForeignKeyKey) $relatedForeignKeyKey = $relatedForeignKeyColumn->getName();

                            if ($relatedForeignKeyColumn instanceof ForeignKeyField) {
                                if (!$relatedForeignKeyColumn->keyIsId($relatedForeignKeyKey)) {
                                    $relatedForeignKeyKey .= 'Id';
                                }
                            }
                        }
                    }

                    if (isset($this->accessPolicy) && $this->accessPolicy->name) {
                        $relatedAccessPolicy = $field->getAssociatedAccessPolicy($this->accessPolicy->name);
                    }
                    if (!$relatedAccessPolicy) $relatedAccessPolicy = 'lkt-related';


                    // Update or create
                    foreach ($data as $datum) {
                        if ($relatedMode && $relatedForeignKeyKey && !$datum[$relatedForeignKeyKey]) {
                            $datum[$relatedForeignKeyKey] = $this->getIdColumnValue();
                        }
                        elseif ($foreignKeysMode && $relatedForeignKeyKey && !$datum[$relatedForeignKeyKey]) {
                            $datum[$relatedForeignKeyKey] = $this->getIdColumnValue();
                        }

                        if ($datum[$relatedIdColumn] > 0) {
                            $ins = $relatedClass::getInstance($datum[$relatedIdColumn]);
                            if ($relatedAccessPolicy) $ins->setAccessPolicy($relatedAccessPolicy, AccessPolicyEndOfLife::UntilNextWrite);
                            $ins->feedAndSave($datum);

                        } else {
                            $ins = $relatedClass::getInstance();
                            if ($relatedAccessPolicy) $ins->setAccessPolicy($relatedAccessPolicy, AccessPolicyEndOfLife::UntilNextWrite);
                            $ins->feedAndSave($datum);
                        }

                        if ($foreignKeysMode) $foreignKeysIds[] = $ins->getId();
                    }

                    if ($foreignKeysMode && count($foreignKeysIds) > 0) {
                        $setter = 'set' . ucfirst($field->getName());
                        $this->{$setter}($foreignKeysIds);
                        $hasToReUpdate = true;
                    }
                }
                $this->PENDING_UPDATE_RELATED_DATA = [];
            }

            if ($hasToReUpdate) {
                $this->save();
            }

            if (isset($this->pivotData)) {
                if ($this->pivotData->hasToSave()) {
                    $this->pivotData->save();
                }

                if ($this->pivotData->hasToLink()) {
                    $this->pivotData->linkPendingPivots();
                }
            }

            if (isset($this->relatedItemData) && $this->relatedItemData->hasToSave()) {
                $this->relatedItemData->save();
            }

            if (isset($this->relatedItemsData)) {
                if ($this->relatedItemsData->hasToSave()) {
                    $this->relatedItemsData->save();
                }

                if ($this->relatedItemsData->hasToAppendItemsInParentForeignKeysField()) {
                    $this->relatedItemsData->appendItemsInParentForeignKeysField();
                }
            }

            // Update composed data
            if (isset($this->composedData) && $this->composedData->hasToSave()) {
                $this->composedData->save();
            }
        }

        if (isset($this->accessPolicy) && $this->accessPolicy->matchedEndOfLife(AccessPolicyEndOfLife::UntilNextWrite)) {
            unset($this->accessPolicy);
        }

        if ($reload) {
            $cacheCode = $schema->getInstanceCode($original);
            InstanceCache::clearCode($cacheCode);
            return Instantiator::make($schema->getComponent(), $this->getIdColumnValue(), $original);
        }

        return $this;
    }


    /**
     * @laminim
     * This methods builds the expected arguments data for a method calling
     * from a given data
     *
     * @param string $method
     * @param array $args
     * @param string $fieldName
     * @return array
     * @throws \ReflectionException
     */
    public function prepareOwnMethodCallArguments(string $method, array $args, string $fieldName): array
    {
        $reflectionMethod = new \ReflectionMethod($this, $method);

        $params = $reflectionMethod->getParameters();

        $paramsKeys = array_map(function (\ReflectionParameter $param){ return $param->getName();}, $params);

        foreach (array_keys($args) as $key) {
            if (!in_array($key, $paramsKeys)) unset($args[$key]);
        }

        return $args;
    }

    public function satisfiedOwnMethodCallArguments(string $method, array $args): bool
    {
        $reflectionMethod = new \ReflectionMethod($this, $method);

        $params = $reflectionMethod->getParameters();

//        if (count($args) < count($params)) return false;

        foreach ($params as $param) {
            // Use array_key_exists over isset because if value is null, isset returns a false positive
            if (!$param->isOptional() && !array_key_exists($param->getName(), $args)) return false;
        }
        return true;
    }

    public function callOwnMethod(string $method, array $args): mixed
    {
        if (count($args) > 0) {
            return call_user_func_array([$this, $method], $args);
        }
        return $this->{$method}();
    }



    public function getUpdatePayload(): array
    {
        $r = [];

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
    public function assignValue(string $key, mixed $value, RetrieveDataMode $mode = RetrieveDataMode::Auto): static
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
            if ($field->isSingleMode()) {
                $this->relatedItemData->setItem($key, (array)$value);
            } else {
                $this->relatedItemsData->setItems($key, (array)$value);
            }

        } elseif ($field instanceof PivotField) {
            $this->pivotData->setItems($key, (array)$value);

        } elseif ($field instanceof RelatedKeysField) {
            if ($key === $field->getAppendForeignKeysName()) {
                $this->relatedItemsData->prepareToAppendItemsInParentForeignKeysField($key, $value);
            }
        }

        return $this;
    }


    public function retrieveValue(string $key, array $additionalData = [], RetrieveDataMode $dataMode = RetrieveDataMode::Raw): mixed
    {

        $schema = $this->getSchema();
        $composedDatum = !$schema->hasFieldDefined($key);
        if ($composedDatum) {
            if (!$this->composedData->hasComposedInstance($key)) {
                $fieldComposingThisField = $schema->getCompositionFieldComposingThisField($key);
                if (!$fieldComposingThisField) return null;

                $relatedComponent = $fieldComposingThisField->getComponent($schema, $this);
                $relatedSchema = Schema::get($relatedComponent);
                $relatedField = $relatedSchema->getField($fieldComposingThisField->getColumn());
                if ($relatedField) {
                    $additionalData[$relatedField->getName()] = $this->getIdColumnValue();
                }

                /** @var Item $composedInstance */
                $composedInstance = $this->composedData->getItem($fieldComposingThisField?->getName(), $additionalData);
                $this->composedData->setComposedInstance($key, $composedInstance);
            }

            $composedInstance = $this->composedData->getComposedInstance($key);

            return $composedInstance->retrieveValue($key, $additionalData);
        }

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
            if (isset($this->composedData)) $additionalData = $this->composedData->prepareAdditionalData($field->getName(), $additionalData);
            return $this->foreignKeyData->getItem($key, $additionalData, $dataMode === RetrieveDataMode::ItemOrAnonymous);

        } elseif ($field instanceof ForeignKeysField) {
            if ($dataMode === RetrieveDataMode::Raw) {
                return $this->foreignKeysData->get($key);
            }
            if ($dataMode === RetrieveDataMode::Ids) {
                return $this->foreignKeysData->getIds($key);
            }
            return $this->foreignKeysData->getItems($key);

        } elseif ($field instanceof RelatedField) {
            if (isset($this->composedData)) $additionalData = $this->composedData->prepareAdditionalData($field->getName(), $additionalData);
            if ($field->isSingleMode()) return $this->relatedItemData->getItem($key, $additionalData, $dataMode === RetrieveDataMode::ItemOrAnonymous);
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
            if ($field->isSingleMode()) return $this->relatedItemData->has($key, $additionalData);
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
        $schema = $this->getSchema();
        $composedDatum = !$schema->hasFieldDefined($key);
        if ($composedDatum) {
            if (!$this->composedData->hasComposedInstance($key)) {
                $fieldComposingThisField = $schema->getCompositionFieldComposingThisField($key);
                if (!$fieldComposingThisField) return null;

                $relatedComponent = $fieldComposingThisField->getComponent($schema, $this);
                $relatedSchema = Schema::get($relatedComponent);
                $relatedField = $relatedSchema->getField($fieldComposingThisField->getColumn());
                if ($relatedField) {
                    $additionalData[$relatedField->getName()] = $this->getIdColumnValue();
                }

                /** @var Item $composedInstance */
                $composedInstance = $this->composedData->getItem($fieldComposingThisField?->getName(), $additionalData);
                $this->composedData->setComposedInstance($key, $composedInstance);
            }

            $composedInstance = $this->composedData->getComposedInstance($key);

            return $composedInstance->readValue($key, $responseKey, $additionalData);
        }

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

            $items = $this->retrieveValue($key, $additionalData, RetrieveDataMode::Item);

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
                $batchActions = $relatedSchema->getBatchActions((array)$items);
                $r[$responseKey] = $batchActions->read($relatedAccessPolicy);
//                $r[$responseKey . 'Ids'] = $this->relatedItemsData->getItemsIds($key);
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
                    $t[] = $item->autoRead();
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
            if ($responseKey === $field->getName()) {
                $defaultName = $field->getColumn();
                if ($defaultName) {
                    $responseKey = $defaultName;
                }
            }
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
                $t[] = $item->autoRead();

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