<?php

namespace Lkt\Factory\Instance\Traits;

use Lkt\Debug\VarDumper;
use Lkt\Factory\Fields\Interfaces\Field;
use Lkt\Factory\Instance\DTO\GroupedData;
use Lkt\Factory\Instance\Enums\RetrieveDataMode;
use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Instantiator\Cache\InstanceCache;
use Lkt\Factory\Instantiator\Enums\CrudOperation;
use Lkt\Factory\Instantiator\Exceptions\UnsetFieldStorePathException;
use Lkt\Factory\Instantiator\Instantiator;
use Lkt\Factory\Instantiator\ValueObjects\ComponentDatabaseIntegration;
use Lkt\Factory\Schemas\Enums\AccessPolicyEndOfLife;
use Lkt\Factory\Schemas\Enums\OnNonWritableSaveAttempt;
use Lkt\Factory\Schemas\Exceptions\InvalidComponentException;
use Lkt\Factory\Schemas\Exceptions\InvalidItemDataAssignException;
use Lkt\Factory\Schemas\Exceptions\MissedMandatoryValueException;
use Lkt\Factory\Schemas\Exceptions\SchemaNotDefinedException;
use Lkt\Factory\Schemas\Fields\BooleanField;
use Lkt\Factory\Schemas\Fields\ColorField;
use Lkt\Factory\Schemas\Fields\ConstantValueField;
use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\FileField;
use Lkt\Factory\Schemas\Fields\FloatField;
use Lkt\Factory\Schemas\Fields\ForeignKeysField;
use Lkt\Factory\Schemas\Fields\IntegerField;
use Lkt\Factory\Schemas\Fields\JSONField;
use Lkt\Factory\Schemas\Fields\MethodGetterField;
use Lkt\Factory\Schemas\Fields\PivotField;
use Lkt\Factory\Schemas\Fields\RelatedField;
use Lkt\Factory\Schemas\Fields\RelatedKeysField;
use Lkt\Factory\Schemas\Fields\StringField;
use Lkt\Factory\Schemas\Fields\ValueListField;
use Lkt\Factory\Schemas\Schema;
use Lkt\Locale\Locale;
use Lkt\Translations\Translations;

trait ItemWithDataTrait
{
    public function initialFeed(array $initialData = [], bool $refreshing = false): static
    {
        $schema = $this->getSchema();
        $groupedData = new GroupedData($schema, $initialData);

        if (property_exists($this, 'stringD')) {
            $this->initStringData($schema, $this, $groupedData->stringData);
        }

        if (property_exists($this, 'booleanD')) {
            $this->initBooleanData($schema, $this, $groupedData->booleanData);
        }

        if (property_exists($this, 'integerD')) {
            $this->initIntegerData($schema, $this, $groupedData->integerData);
        }

        if (property_exists($this, 'integerMD')) {
            $this->initMultipleIntegerData($schema, $this, $groupedData->multipleIntegerData);
        }

        if (property_exists($this, 'floatD')) {
            $this->initFloatData($schema, $this, $groupedData->floatData);
        }

        if (property_exists($this, 'floatMD')) {
            $this->initMultipleFloatData($schema, $this, $groupedData->multipleFloatData);
        }

        if (property_exists($this, 'dateD')) {
            $this->initDateData($schema, $this, $groupedData->dateData);
        }

        if (property_exists($this, 'colorD')) {
            $this->initColorData($schema, $this, $groupedData->colorData);
        }

        if (property_exists($this, 'encryptD')) {
            $this->initEncryptData($schema, $this, $groupedData->encryptData);
        }

        if (property_exists($this, 'foreignKeyD')) {
            $this->initForeignKeyData($schema, $this, $groupedData->foreignKeyData);
        }

        if (property_exists($this, 'foreignKeysData')) {
            $this->initForeignKeysData($schema, $this, $groupedData->foreignKeysData);
        }

        if (property_exists($this, 'relatedItemD')) {
            $this->initRelatedItemData($schema, $this, $groupedData->relatedItemData);
        }

        if (property_exists($this, 'jsonD')) {
            $this->initJSONData($schema, $this, $groupedData->jsonData);
        }

        if (property_exists($this, 'fileD')) {
            $this->initFileData($schema, $this, $groupedData->fileData);
        }

        if (property_exists($this, 'stringMD')) {
            $this->initMultipleStringData($schema, $this, $groupedData->multipleStringData);
        }

        if (property_exists($this, 'relatedItemsD')) {
            $this->initRelatedItemsData($schema, $this, $groupedData->relatedItemsData, $refreshing);
        }

        if (property_exists($this, 'pivotD')) {
            $this->initPivotData($schema, $this, $refreshing);
        }

        if (property_exists($this, 'composedD')) {
            $this->initComposedData($schema, $this, $refreshing);
        }

        if (property_exists($this, 'constantD')) {
            $this->initConstantData($schema, $this);
        }

        if (property_exists($this, 'concatD')) {
            $this->initConcatData($schema, $this);
        }

        $this->clearIdentifierValue();

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
        $operation = $this->isAnonymous() ? CrudOperation::Create : CrudOperation::Update;
        $data = $this->prepareCrudData($data, $operation);

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

                // Pivot table data linking
                if ($isPivotDatumFeed) {
                    $this->pivotD->prepareToLink($field->getName(), $value);
                    continue;
                }
            }

            if (!$field || $field instanceof MethodGetterField) continue;

            if ($field instanceof StringField) {
                if ($field->isConcatenation() || $field->isTranslation()) {
                    continue;
                }
            }

            if ($field instanceof PivotField && $field->keyIsIds($param)) {
                $this->assignValue($field->getName(), $value, RetrieveDataMode::Ids);
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

                $fieldComposingThisField = $schema->getCompositionFieldComposingThisField($param);
                if (!$fieldComposingThisField) {
                    // Field composed with an alias
                    $fieldComposingThisField = $schema->getCompositionFieldComposingThisField($field->getName());
                    $composedKey = $field->getName();
                }
                if (!$fieldComposingThisField) continue;

                $associatedAccessPolicy = $accessPolicyUsage ? $fieldComposingThisField->getAssociatedAccessPolicy($accessPolicyUsage->name) : '';

                $composedInstanceKey = $fieldComposingThisField?->getName();

                $additionalData = $this->composedD->prepareAdditionalData($composedKey, $internalMethodsArguments);

                if (!$composedInstances[$composedInstanceKey]) {
                    /** @var Item $composedInstance */
                    $composedInstance = $this->composedD->getItem($fieldComposingThisField?->getName(), $additionalData);
                    $composedInstances[$composedInstanceKey] = $composedInstance;
                    $this->composedD->setComposedInstance($composedInstanceKey, $composedInstance);
                }

                if ($associatedAccessPolicy) {
                    $composedInstances[$composedInstanceKey]->setAccessPolicy($associatedAccessPolicy);
                }

                $composedInstances[$composedInstanceKey]->feed([
                    $composedKey => $value,
                ], $additionalData);
                continue;
            }

            // Common primitive value fields (included composed elements thanks to generated setter detection  approach)
            $key = $field instanceof RelatedKeysField && $param === $field->getAppendForeignKeysName() ? $param : $field->getName();
            $this->assignValue($key, $value);
        }

        if (count($composedInstances) > 0) {
            $this->composedD->setItems($composedInstances);
        }

        return $this;
    }

    public function feedAndSave(array $data, array $internalMethodsArguments = []): static
    {
        return $this->feed($data, $internalMethodsArguments)->save();
    }


    public function save(): static
    {
        $schema = $this->getSchema();

        if ($schema->hasDatabaseViewContext()) {
            $action = $schema->getOnNonWritableSaveAttempt();
            if ($action === OnNonWritableSaveAttempt::Error) throw new \Exception('Invalid attempt to write data on a view');
            return $this;
        }

        $isUpdate = !$this->isAnonymous();
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
        /** @var Field[] $fieldsWithDefaultValue */
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
                if (array_key_exists($key, $payload)) unset($payload[$key]);
            }
        }


        $fileFields = $schema->getFileFields();

        $pendingUploadBase64Files = [];
        $pendingUploadBase64MultipleFiles = [];
        if (count($payload) > 0) {
            // Check if it's needed to store a base64 file:
            foreach ($fileFields as $fileField) {
                if ($this->fileD->updatedWithBase64String($fileField->getName())) {
                    $storePath = $fileField->getStorePath($this);
                    if ($storePath === '') {
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
            if (!$this->hasAssignedValue($mandatoryField->getName())) {
                $additionalFieldsToColumn = array_filter($schema->getFields(), function (Field $field) use ($mandatoryField) {
                    return $field->getName() !== $mandatoryField->getName()
                        && $field->getColumn() === $mandatoryField->getColumn();
                });
                $ok = false;
                if (count($additionalFieldsToColumn) > 0) {
                    foreach ($additionalFieldsToColumn as $additionalFieldToColumn) {
                        $ok = $ok || $this->hasAssignedValue($additionalFieldToColumn->getName());
                    }
                }

                if (!$ok) {
                    throw MissedMandatoryValueException::getInstance($schema->getComponent() . '.' . $mandatoryField->getName());
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
                    $this->fileD->base64ToFile($fileFieldName, $fileFieldValue);
                    $hasToReUpdate = true;
                }
            }

            if (count($pendingUploadBase64MultipleFiles) > 0) {
                foreach ($pendingUploadBase64MultipleFiles as $fileFieldName => $fileFieldValue) {
                    $this->fileD->base64ToFiles($fileFieldName, $fileFieldValue);
                    $hasToReUpdate = true;
                }
            }

            if (isset($this->fileD) && $this->fileD->hasPendingHttpUploads()) {
                foreach ($fileFields as $fileField) {
                    $this->fileD->httpUploadToFile($fileField->getName());
                }
            }

            if ($hasToReUpdate) {
                $this->save();
            }

            if (isset($this->pivotD)) {
                if ($this->pivotD->hasToSave()) {
                    $this->pivotD->save();
                }

                if ($this->pivotD->hasToLink()) {
                    $this->pivotD->linkPendingPivots();
                }
            }

            if (isset($this->relatedItemD) && $this->relatedItemD->hasToSave()) {
                $this->relatedItemD->save();
            }

            if (isset($this->relatedItemsD)) {
                if ($this->relatedItemsD->hasToSave()) {
                    $this->relatedItemsD->save();
                }

                if ($this->relatedItemsD->hasToAppendItemsInParentForeignKeysField()) {
                    $this->relatedItemsD->appendItemsInParentForeignKeysField();
                }
            }

            // Update composed data
            if (isset($this->composedD) && $this->composedD->hasToSave()) {
                $this->composedD->save();
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

        $paramsKeys = array_map(function (\ReflectionParameter $param) {
            return $param->getName();
        }, $params);

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

        if (property_exists($this, 'stringD')) {
            foreach ($this->stringD->getPayload() as $k => $v) $r[$k] = $v;
        }

        if (property_exists($this, 'integerD')) {
            foreach ($this->integerD->getPayload() as $k => $v) $r[$k] = $v;
        }

        if (property_exists($this, 'integerMD')) {
            foreach ($this->integerMD->getPayload() as $k => $v) $r[$k] = $v;
        }

        if (property_exists($this, 'floatD')) {
            foreach ($this->floatD->getPayload() as $k => $v) $r[$k] = $v;
        }

        if (property_exists($this, 'floatMD')) {
            foreach ($this->floatMD->getPayload() as $k => $v) $r[$k] = $v;
        }

        if (property_exists($this, 'booleanD')) {
            foreach ($this->booleanD->getPayload() as $k => $v) $r[$k] = $v;
        }

        if (property_exists($this, 'dateD')) {
            foreach ($this->dateD->getPayload() as $k => $v) $r[$k] = $v;
        }

        if (property_exists($this, 'colorD')) {
            foreach ($this->colorD->getPayload() as $k => $v) $r[$k] = $v;
        }

        if (property_exists($this, 'encryptD')) {
            foreach ($this->encryptD->getPayload() as $k => $v) $r[$k] = $v;
        }

        if (property_exists($this, 'foreignKeyD')) {
            foreach ($this->foreignKeyD->getPayload() as $k => $v) $r[$k] = $v;
        }

        if (property_exists($this, 'foreignKeysData')) {
            foreach ($this->foreignKeysData->getPayload() as $k => $v) $r[$k] = $v;
        }

        if (property_exists($this, 'jsonD')) {
            foreach ($this->jsonD->getPayload() as $k => $v) $r[$k] = $v;
        }

        if (property_exists($this, 'fileD')) {
            foreach ($this->fileD->getPayload() as $k => $v) $r[$k] = $v;
        }

        return $r;
    }

    public function getOriginalData(): array
    {
        $r = [];

        if (property_exists($this, 'stringD')) {
            foreach ($this->stringD->getOriginalData() as $k => $v) $r[$k] = $v;
        }

        if (property_exists($this, 'integerD')) {
            foreach ($this->integerD->getOriginalData() as $k => $v) $r[$k] = $v;
        }

        if (property_exists($this, 'integerMD')) {
            foreach ($this->integerMD->getOriginalData() as $k => $v) $r[$k] = $v;
        }

        if (property_exists($this, 'floatD')) {
            foreach ($this->floatD->getOriginalData() as $k => $v) $r[$k] = $v;
        }

        if (property_exists($this, 'floatMD')) {
            foreach ($this->floatMD->getOriginalData() as $k => $v) $r[$k] = $v;
        }

        if (property_exists($this, 'booleanD')) {
            foreach ($this->booleanD->getOriginalData() as $k => $v) $r[$k] = $v;
        }

        if (property_exists($this, 'dateD')) {
            foreach ($this->dateD->getOriginalData() as $k => $v) $r[$k] = $v;
        }

        if (property_exists($this, 'colorD')) {
            foreach ($this->colorD->getOriginalData() as $k => $v) $r[$k] = $v;
        }

        if (property_exists($this, 'encryptD')) {
            foreach ($this->encryptD->getOriginalData() as $k => $v) $r[$k] = $v;
        }

        if (property_exists($this, 'foreignKeyD')) {
            foreach ($this->foreignKeyD->getOriginalData() as $k => $v) $r[$k] = $v;
        }

        if (property_exists($this, 'foreignKeysData')) {
            foreach ($this->foreignKeysData->getOriginalData() as $k => $v) $r[$k] = $v;
        }

        if (property_exists($this, 'jsonD')) {
            foreach ($this->jsonD->getOriginalData() as $k => $v) $r[$k] = $v;
        }

        if (property_exists($this, 'fileD')) {
            foreach ($this->fileD->getOriginalData() as $k => $v) $r[$k] = $v;
        }

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
    public function assignValue(string $key, mixed $value, RetrieveDataMode $mode = RetrieveDataMode::Auto, array $internalMethodsArguments = []): static
    {
        $field = $this->getSchema()->getField($key);
        if (!$field) throw InvalidItemDataAssignException::missingField($key);

        if ($field instanceof StringField) {
            if ($field->isTranslation()) {
                return $this;
            }

            if ($field->isEncrypted()) {
                $this->encryptD->set($key, $value);
            } else {
                $this->stringD->set($key, $value);
            }

        } elseif ($field instanceof IntegerField) {
            if ($field->isForeignKey()) {
                $this->foreignKeyD->set($key, $value);
            }
            elseif ($field->isMultiple()) {
                $this->integerMD->set($key, $value);
            } else {
                $this->integerD->set($key, $value);
            }

        } elseif ($field instanceof FloatField) {
            if ($field->isMultiple()) {
                $this->floatMD->set($key, $value);
            } else {
                $this->floatD->set($key, $value);
            }

        } elseif ($field instanceof BooleanField) {
            $this->booleanD->set($key, $value);

        } elseif ($field instanceof DateTimeField) {
            $this->dateD->set($key, $value);

        } elseif ($field instanceof JSONField) {
            $this->jsonD->set($key, $value);

        } elseif ($field instanceof ColorField) {
            $this->colorD->set($key, $value);

        } elseif ($field instanceof FileField) {
            $this->fileD->set($key, $value);

        } elseif ($field instanceof RelatedField) {
            if ($field->isSingleMode()) {
                $this->relatedItemD->setItem($key, (array)$value);
            } else {
                $this->relatedItemsD->setItems($key, (array)$value);
            }

        } elseif ($field instanceof PivotField) {
            if ($mode === RetrieveDataMode::Ids) {
                $this->pivotD->setItemsIds($key, (array)$value);
            } else {
                $this->pivotD->setItems($key, (array)$value);
            }

        } elseif ($field instanceof ForeignKeysField) {
            $this->foreignKeysData->set($key, $value);

        } elseif ($field instanceof RelatedKeysField) {
            if ($key === $field->getAppendForeignKeysName()) {
                $this->relatedItemsD->prepareToAppendItemsInParentForeignKeysField($key, $value);
            }
        }

        return $this;
    }


    public function retrieveValue(string $key, array $additionalData = [], RetrieveDataMode $dataMode = RetrieveDataMode::Raw): mixed
    {
        $schema = $this->getSchema();
        $composedDatum = !$schema->hasFieldDefined($key);
        if ($composedDatum) {
            if (!$this->composedD->hasComposedInstance($key)) {
                $fieldComposingThisField = $schema->getCompositionFieldComposingThisField($key);
                if (!$fieldComposingThisField) return null;

                $relatedComponent = $fieldComposingThisField->getComponent($schema, $this);
                $relatedSchema = Schema::get($relatedComponent);
                $relatedField = $relatedSchema->getField($fieldComposingThisField->getColumn());
                if ($relatedField) {
                    $additionalData[$relatedField->getName()] = $this->getIdColumnValue();
                }

                /** @var Item $composedInstance */
                $composedInstance = $this->composedD->getItem($fieldComposingThisField?->getName(), $additionalData);
                $this->composedD->setComposedInstance($key, $composedInstance);
            }

            $composedInstance = $this->composedD->getComposedInstance($key);

            return $composedInstance->retrieveValue($key, $additionalData, $dataMode);
        }

        $field = $this->getSchema()->getField($key);
        if (!$field) throw InvalidItemDataAssignException::missingField($key);

        if ($field instanceof StringField) {
            if ($field->isConcatenation()) return $this->concatD->get($key);
            if ($field->isEncrypted()) return $this->encryptD->get($key);
            return $this->stringD->get($key);

        } elseif ($field instanceof FloatField) {
            if ($field->isMultiple()) {
                return $this->floatMD->get($key);
            } else {
                return $this->floatD->get($key);
            }

        } elseif ($field instanceof BooleanField) {
            return $this->booleanD->get($key);

        } elseif ($field instanceof DateTimeField) {
            return $this->dateD->get($key);

        } elseif ($field instanceof JSONField) {
            return $this->jsonD->get($key);


        } elseif ($field instanceof ColorField) {
            return $this->colorD->get($key);

        } elseif ($field instanceof FileField) {
            if ($dataMode === RetrieveDataMode::Raw) {
                return $this->fileD->get($key);
            }

            $index = 0;
            if ($field->isMultiple() && array_key_exists('index', $additionalData)) {
                $index = (int)$additionalData['index'];
            }
            if ($dataMode === RetrieveDataMode::FileContent) return $this->fileD->getFileContent($key, $index);
            if ($dataMode === RetrieveDataMode::FileExtension) return $this->fileD->getFileExtension($key, $index);
            if ($dataMode === RetrieveDataMode::FileLastModified) return $this->fileD->getFileLastModified($key, $index);
            if ($dataMode === RetrieveDataMode::FileName) return $this->fileD->getFileName($key, $index);
            if ($dataMode === RetrieveDataMode::FileSize) return $this->fileD->getFileSize($key, $index);
            if ($dataMode === RetrieveDataMode::FileInternalPath) return $this->fileD->getInternalPath($key);
            return $this->fileD->getPublicPath($key);

        } elseif ($field instanceof RelatedField) {
            if (isset($this->composedD)) {
                $additionalData = $this->composedD->prepareAdditionalData($field->getName(), $additionalData);
            }

            if ($field->isSingleMode()) {
                return $this->relatedItemD->getItem($key, $additionalData, $dataMode === RetrieveDataMode::ItemOrAnonymous);
            }

            return $this->relatedItemsD->getItems($key);

        } elseif ($field instanceof ConstantValueField) {
            return $this->constantD->get($key);

        } elseif ($field instanceof PivotField) {
            return $this->pivotD->getItems($key);

        } elseif ($field instanceof IntegerField) {
            if ($field->isForeignKey()) {

                if ($dataMode === RetrieveDataMode::Raw) {
                    return $this->foreignKeyD->get($key);
                }
                if (isset($this->composedD)) $additionalData = $this->composedD->prepareAdditionalData($field->getName(), $additionalData);
                return $this->foreignKeyD->getItem($key, $additionalData, $dataMode === RetrieveDataMode::ItemOrAnonymous);

            } elseif ($field->isMultiple()) {
                return $this->integerMD->get($key);
            } else {
                return $this->integerD->get($key);
            }

        } elseif ($field instanceof ForeignKeysField) {
            if ($dataMode === RetrieveDataMode::Raw) {
                return $this->foreignKeysData->get($key);
            }
            if ($dataMode === RetrieveDataMode::Ids) {
                return $this->foreignKeysData->getIds($key);
            }
            return $this->foreignKeysData->getItems($key);

        } elseif ($field instanceof RelatedKeysField) {
            return $this->relatedItemsD->getItems($key);
        }
        return null;
    }

    public function hasAssignedValue(string $key, array $additionalData = []): bool
    {
        $field = $this->getSchema()->getField($key);
        if (!$field) throw InvalidItemDataAssignException::missingField($key);

        if ($field instanceof StringField) {
            if ($field->isConcatenation()) return $this->concatD->has($key);
            if ($field->isEncrypted()) return $this->encryptD->has($key);
            return $this->stringD->has($key);

        } elseif ($field instanceof FloatField) {
            if ($field->isMultiple()) {
                return $this->floatMD->has($key);
            } else {
                return $this->floatD->has($key);
            }

        } elseif ($field instanceof BooleanField) {
            return $this->booleanD->has($key);

        } elseif ($field instanceof DateTimeField) {
            return $this->dateD->has($key);

        } elseif ($field instanceof JSONField) {
            return $this->jsonD->has($key);


        } elseif ($field instanceof ColorField) {
            return $this->colorD->has($key);

        } elseif ($field instanceof FileField) {
            return $this->fileD->has($key);

        } elseif ($field instanceof RelatedField) {
            if ($field->isSingleMode()) return $this->relatedItemD->has($key, $additionalData);
            return $this->relatedItemsD->has($key);

        } elseif ($field instanceof ConstantValueField) {
            return $this->constantD->has($key);

        } elseif ($field instanceof PivotField) {
            return $this->pivotD->has($key);

        } elseif ($field instanceof IntegerField) {
            if ($field->isForeignKey()){
                return $this->foreignKeyD->has($key);
            } elseif ($field->isMultiple()) {
                return $this->integerMD->has($key);
            } else {
                return $this->integerD->has($key);
            }

        } elseif ($field instanceof ForeignKeysField) {
            return $this->foreignKeysData->has($key);

        } elseif ($field instanceof RelatedKeysField) {
            return $this->relatedItemsD->has($key);
        }

        return false;
    }

    public function readValue(string $key, string $responseKey, array $additionalData = []): array|null
    {
        $schema = $this->getSchema();
        $composedDatum = !$schema->hasFieldDefined($key);
        if ($composedDatum) {
            if (!$this->composedD->hasComposedInstance($key)) {
                $fieldComposingThisField = $schema->getCompositionFieldComposingThisField($key);
                if (!$fieldComposingThisField) return null;

                $relatedComponent = $fieldComposingThisField->getComponent($schema, $this);
                $relatedSchema = Schema::get($relatedComponent);
                $relatedField = $relatedSchema->getField($fieldComposingThisField->getColumn());
                if ($relatedField) {
                    $additionalData[$relatedField->getName()] = $this->getIdColumnValue();
                }

                /** @var Item $composedInstance */
                $composedInstance = $this->composedD->getItem($fieldComposingThisField?->getName(), $additionalData);
                $this->composedD->setComposedInstance($key, $composedInstance);
            }

            $composedInstance = $this->composedD->getComposedInstance($key);

            return $composedInstance->readValue($key, $responseKey, $additionalData);
        }

        $field = $this->getSchema()->getField($key);
        if (!$field) throw InvalidItemDataAssignException::missingField($key);

        if ($field instanceof StringField) {
            if ($field->isConcatenation()) {
                return [$responseKey => $this->concatD->get($key)];
            }

            if ($field->isEncrypted()) {
                return [$responseKey => $this->encryptD->get($key)];
            }

            if ($field->ableToChoose()) {
                $r = [];
                $value = $this->stringD->get($field->getName());
                $r[$responseKey] = $value;
                $i18nOptions = $field->getI18nViewOptions();
                if ($i18nOptions !== '') {
                    $r[$responseKey . 'Text'] = Translations::get($i18nOptions . ".{$value}", Locale::getLangCode());
                }
                return $r;
            }
            return [$responseKey => $this->stringD->get($key)];

        } elseif ($field instanceof IntegerField) {
            if ($field->isForeignKey()) {
                $relatedAccessPolicy = null;
                $accessPolicyUsage = $this->getAccessPolicyUsage();
                $schema = $this->getSchema();

                if ($accessPolicyUsage) {
                    $relatedAccessPolicy = $schema->getAccessPolicyForRelationalField($this->accessPolicy, $field);
                }

                if (!$relatedAccessPolicy && Schema::get($field->getComponent($schema, $this))->hasRelatedAccessPolicy()) {
                    $relatedAccessPolicy = 'lkt-related';
                }

                if ($field->hasCompositionContent()) {
                    $additionalData = $this->composedD->prepareAdditionalData($field->getName(), $additionalData);
                }

                $item = $this->foreignKeyD->getItem($key, $additionalData);
                if ($item instanceof Item) {
                    if ($relatedAccessPolicy) $item->setAccessPolicy($relatedAccessPolicy, AccessPolicyEndOfLife::UntilNextRead);
                    $item = $item->autoRead();
                }

                $r = [];
                if (!is_array($item)) $item = [];
                $r[$responseKey] = $item;
                $r[$responseKey . 'Id'] = $this->foreignKeyD->get($key);
                if ($field->hasOnReadIncludeOptions()) {
                    $r[$responseKey . 'Opts'] = count($item) > 0 ? [$item] : [];
                }

                return $r;

            } elseif ($field->isMultiple()) {
                return [$responseKey => $this->integerMD->get($key)];
            } else {
                return [$responseKey => $this->integerD->get($key)];
            }

        } elseif ($field instanceof BooleanField) {
            return [$responseKey => $this->booleanD->get($key)];

        } elseif ($field instanceof DateTimeField) {
            return [$responseKey => $this->dateD->get($key)];

        } elseif ($field instanceof JSONField) {
            return [$responseKey => $this->jsonD->get($key)];

        } elseif ($field instanceof ColorField) {
            return [$responseKey => $this->colorD->get($key)];

        } elseif ($field instanceof FileField) {
            return [$responseKey => $this->fileD->getPublicPath($key)];

        } elseif ($field instanceof RelatedField) {

            $r = [];
            $relatedSchema = Schema::get($field->getComponent());

            // @todo test this as a replacement for complex primary key. If not working, remove this code block
            if ($relatedSchema->hasManyIdentifiers()) {
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

            if ($relatedAccessPolicy) {
                $customRelationAccessPolicy = $field->getAssociatedAccessPolicy($relatedAccessPolicy);
                if ($customRelationAccessPolicy) $relatedAccessPolicy = $customRelationAccessPolicy;
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
//                $r[$responseKey . 'Ids'] = $this->relatedItemsD->getItemsIds($key);
            }


            return $r;

        } elseif ($field instanceof FloatField) {
            if ($field->isMultiple()) {
                return [$responseKey => $this->floatMD->get($key)];
            } else {
                return [$responseKey => $this->floatD->get($key)];
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
            /** @var Item[] $items */
            $items = $this->pivotD->getItems($field->getName());
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
            $r[$responseKey . 'Ids'] = $this->pivotD->getItemsIds($key);
            return $r;

        } elseif ($field instanceof ValueListField) {
            $r = [];

            if ($field->readModeIsBoth()) {
                $r[$responseKey] = $this->stringMD->getAsString($field->getName());
                $r[$responseKey . 'List'] = $this->stringMD->get($field->getName());

            } elseif ($field->readModeIsString()) {
                $r[$responseKey] = $this->stringMD->getAsString($field->getName());

            } elseif ($field->readModeIsArray()) {
                $r[$responseKey] = $this->stringMD->get($field->getName());
            }
            return $r;

        } elseif ($field instanceof ConstantValueField) {
            return [$responseKey => $this->constantD->get($key)];

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


            $items = $this->relatedItemsD->getItems($key);
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
                $r[$responseKey . 'Ids'] = $this->relatedItemsD->getItemsIds($key);;
            }


            return $r;
        }

        return null;
    }
}