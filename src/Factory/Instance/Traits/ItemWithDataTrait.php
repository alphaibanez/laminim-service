<?php

namespace Lkt\Factory\Instance\Traits;

use Lkt\Debug\VarDumper;
use Lkt\Factory\Instantiator\Cache\InstanceCache;
use Lkt\Factory\Instantiator\ComponentId;
use Lkt\Factory\Instantiator\Enums\CrudOperation;
use Lkt\Factory\Instantiator\Exceptions\UnsetFieldStorePathException;
use Lkt\Factory\Instantiator\Helpers\FileUploadHelper;
use Lkt\Factory\Instantiator\Instances\AbstractInstance;
use Lkt\Factory\Instantiator\Instantiator;
use Lkt\Factory\Instantiator\ValueObjects\ComponentDatabaseIntegration;
use Lkt\Factory\Schemas\Enums\AccessPolicyEndOfLife;
use Lkt\Factory\Schemas\Exceptions\MissedMandatoryValueException;
use Lkt\Factory\Schemas\Fields\AbstractField;
use Lkt\Factory\Schemas\Fields\ConcatField;
use Lkt\Factory\Schemas\Fields\FloatField;
use Lkt\Factory\Schemas\Fields\ForeignKeyField;
use Lkt\Factory\Schemas\Fields\ForeignKeysField;
use Lkt\Factory\Schemas\Fields\HTMLField;
use Lkt\Factory\Schemas\Fields\IdField;
use Lkt\Factory\Schemas\Fields\IntegerField;
use Lkt\Factory\Schemas\Fields\MethodGetterField;
use Lkt\Factory\Schemas\Fields\PivotField;
use Lkt\Factory\Schemas\Fields\PivotLeftIdField;
use Lkt\Factory\Schemas\Fields\PivotPositionField;
use Lkt\Factory\Schemas\Fields\RelatedField;
use Lkt\Factory\Schemas\Fields\RelatedKeysField;
use Lkt\Factory\Schemas\Fields\StringField;
use Lkt\Factory\Schemas\Schema;
use function Lkt\Tools\Arrays\compareArrays;
use function Lkt\Tools\Parse\clearInput;

trait ItemWithDataTrait
{
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
                    $fieldComposingThisField = $schema->getCompositionFieldComposingThisField($param);
                    if (!$fieldComposingThisField) continue;
                    /** @var AbstractInstance $composedInstance */
                    $composedInstance = $this->_getCompositionInstance($fieldComposingThisField?->getName(), $internalMethodsArguments);
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

            if ($field instanceof PivotField) {
                if ($isPivotDatumFeed) {
                    $setter = '_setPendingPivotLink';
                    $methodCallData = ['field' => $field->getName(), 'relatedId' => (int)$value];
                } else {
                    $setter = '_setPivotSort';
                    $methodCallData = ['column' => $field->getName(), 'data' => $value];
                }
            } else {
                $this->assignValue($field->getName(), $value);
            }
//            continue;
//
//            if ($field instanceof RelatedField) {
//                $setter = '_setRelatedValWithData';
//                $methodCallData = ['type' => '', 'column' => $field->getName(), 'data' => $value];
//                if ($field->isSingleMode()) {
//                    $methodCallData['data'] = [$methodCallData['data']];
//                }
//
//            } elseif ($field instanceof ForeignKeyField) {
//                if ($field->keyIsId($param)) {
//                    $setter = '_setIntegerVal';
//                    $methodCallData = ['fieldName' => $field->getName() . 'Id', 'value' => $value];
//                }
//                elseif (is_numeric($value)) {
//                    $setter = '_setIntegerVal';
//                    $methodCallData = ['fieldName' => $field->getName(), 'value' => (int)$value];
//                } elseif (is_array($value)) {
//                    $relatedSchema = Schema::get($field->getComponent($schema, $this));
//                    $relatedIdFields = $relatedSchema->getIdentifiers();
//                    if (count($relatedIdFields) === 1) {
//                        $relatedIdKey = $relatedIdFields[0]->getName();
//                        $relatedId = isset($value[$relatedIdKey]) ? (int)$value[$relatedIdKey] : 0;
//                        if ($relatedId > 0) {
//                            $setter = '_setIntegerVal';
//                            $methodCallData = ['fieldName' => $field->getName(), 'value' => $relatedId];
//                        }
//                    }
//                } else {
//                    continue;
//                }
//
//            } elseif ($field instanceof ForeignKeysField) {
//                if ($field->keyIsIds($param)) {
//                    $setter = '_setForeignListVal';
//                    $methodCallData = ['fieldName' => $field->getName(), 'value' => $value];
//
//                } elseif (is_array($value) && isset($value[0]) && is_numeric($value[0])) {
//                    $setter = '_setForeignListVal';
//                    $methodCallData = ['fieldName' => $field->getName(), 'value' => $value];
//
//                } else {
//                    $setter = '_setForeignListWithData';
//                    $methodCallData = ['fieldName' => $field->getName(), 'data' => $value];
//                }
//
//            } elseif ($field instanceof RelatedKeysField) {
//                if ($param === $field->getAppendForeignKeysName() && ((is_numeric($value) && $value > 0) || is_array($value))) {
//                    $setter = '_appendToParentForeignKeys';
//                    $methodCallData = ['field' => $field->getName(), 'parentValue' => $value];
//                } else {
//                    continue;
//                }
//
//            } elseif ($field instanceof PivotField) {
//                if ($isPivotDatumFeed) {
//                    $setter = '_setPendingPivotLink';
//                    $methodCallData = ['field' => $field->getName(), 'relatedId' => (int)$value];
//                } else {
//                    $setter = '_setPivotSort';
//                    $methodCallData = ['column' => $field->getName(), 'data' => $value];
//                }
//
//            } else if ($field instanceof StringField || $field instanceof HTMLField) {
//                if ($field->isI18nJson() && is_array($value)) {
//                    $translationField = $schema->getField("{$field->getName()}Data");
//                    if ($translationField) {
//                        $setter = '_setJsonVal';
//                        $methodCallData = ['fieldName' => $translationField->getName(), 'value' => $value];
//                    }
//
//                } else {
//                    $methodCallData = [$field->getName() => clearInput($value)];
//                }
//
//            } elseif ($field instanceof IntegerField && !$field instanceof IdField && !$field->isMultiple()) {
//                $methodCallData = [$field->getName() => (int)$value];
//
//            } elseif ($field instanceof FloatField) {
//                $methodCallData = [$field->getName() => (float)$value];
//
//            } else {
//                $methodCallData = [$field->getName() => $value];
//            }

            if (!is_array($methodCallData)) {
                continue;
            }

            $methodCallData = [...$internalMethodsArguments, ...$methodCallData];

            $methodCallData = $this->prepareOwnMethodCallArguments($setter, $methodCallData, $field->getName());
            if (!$this->satisfiedOwnMethodCallArguments($setter, $methodCallData)) {
                continue;
            }
            $this->callOwnMethod($setter, $methodCallData);
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

        $dbIntegration = ComponentDatabaseIntegration::from(static::COMPONENT);
        $queryBuilder = $dbIntegration->query;
        $connection = $dbIntegration->databaseConnector;
        $schema = $dbIntegration->schema;

        if (isset($this->foreignKeysData) && $this->foreignKeysData->hasToSave()) {
            $this->foreignKeysData->save();
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

        // Update only: auto update values
        if ($isUpdate) {
            /** @var AbstractField $fieldsWithDefaultValue */
            $fieldsWithDefaultValue = $schema->getFieldsToUpdateOnInstanceUpdate();
            foreach ($fieldsWithDefaultValue as $fieldWithDefaultValue) {
                $defaultValueKey = $fieldWithDefaultValue->getName();
                if (isset($payload[$defaultValueKey])) continue;

                $defaultValueKey = $fieldWithDefaultValue->getName().'Id';
                if (isset($payload[$defaultValueKey])) continue;

                $defaultValue = $fieldWithDefaultValue->getOnInstanceUpdateValue();
                $setter = $fieldWithDefaultValue->getSetterForPrimitiveValue();
                $this->{$setter}($defaultValue);
            }

            // Create only: set default values
        } else {
            /** @var AbstractField $fieldsWithDefaultValue */
            $fieldsWithDefaultValue = $schema->getFieldsWithDefaultValue();
            foreach ($fieldsWithDefaultValue as $fieldWithDefaultValue) {
                $defaultValueKey = $fieldWithDefaultValue->getName();
                if (isset($payload[$defaultValueKey])) continue;

                $defaultValueKey = $fieldWithDefaultValue->getName().'Id';
                if (isset($payload[$defaultValueKey])) continue;

                $defaultValue = $fieldWithDefaultValue->getDefaultValue();
                $setter = $fieldWithDefaultValue->getSetterForPrimitiveValue();
                $this->{$setter}($defaultValue);
            }
        }


        $fileFields = $schema->getFileFields();

        $pendingUploadBase64Files = [];
        $pendingUploadBase64MultipleFiles = [];
        if (count($payload) > 0) {
            // Check if it's needed to store a base64 file:
            foreach ($fileFields as $fileField) {
                if ($this->_fileValUpdatedWithBase64Data($fileField->getName())) {
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

        $id = 0;

        if (count($payload) > 0) {

            // Save current instance process
            $queryBuilder->updateData($parsed);

            if ($isUpdate) {
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

            } elseif ($original[$origIdColumn] > 0) {
                $id = $original[$origIdColumn];
            }

            if ($queryResponse !== false) {
                $updatedData = array_merge($original, $payload);
                $this->initialFeed($updatedData);
            }
        }

        $hasToReUpdate = false;

        if (count($pendingUploadBase64Files) > 0) {
            foreach ($pendingUploadBase64Files as $fileFieldName => $fileFieldValue) {
                $this->_storeBase64DataAsFile($fileFieldName, $fileFieldValue, $id);
                $hasToReUpdate = true;
            }
        }

        if (count($pendingUploadBase64MultipleFiles) > 0) {
            foreach ($pendingUploadBase64MultipleFiles as $fileFieldName => $fileFieldValue) {
                $this->_storeBase64DataAsFiles($fileFieldName, $fileFieldValue, $id);
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
                $relatedComponent = $field->getComponent();

                if ($field instanceof ForeignKeysField && count($data) === 0) {
                    $currentItems = $this->_getForeignListData($column);
                    if (count($currentItems) === 0) continue;
                }

                if (method_exists($field, 'getDynamicComponentField')) { // Check due to RelatedField not implementing this feature yet
                    $dynamicComponentFieldName = $field->getDynamicComponentField();
                    if ($dynamicComponentFieldName !== '') {
                        $dynamicComponentField = $schema->getField($dynamicComponentFieldName);
                        $getter = $dynamicComponentField->getGetterForPrimitiveValue();
                        $dynamicType = $this->{$getter}();
                        if (is_numeric($dynamicType)) $relatedComponent = ComponentId::getComponent((int)$dynamicType);
                        elseif ($dynamicType !== '') $relatedComponent = $dynamicType;
                    }
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
                        $ins->autoUpdate($datum);

                    } else {
                        $ins = $relatedClass::getInstance();
                        if ($relatedAccessPolicy) $ins->setAccessPolicy($relatedAccessPolicy, AccessPolicyEndOfLife::UntilNextWrite);
                        $ins->autoCreate($datum);
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

        if (count($this->PIVOT_SORT) > 0) {
            foreach ($this->PIVOT_SORT as $column => $ids) {

                $ownField = $schema->getPivotField($column);

                // Pivot table fields (intermediate table)
                $pivotSchema = $ownField->getPivotSchema();

                $pointingField = $pivotSchema->getOneFieldPointingToComponent(static::COMPONENT);

                if ($pointingField instanceof PivotLeftIdField) {
                    $referencedField = $pivotSchema->getPivotRightIdField();
                } else {
                    $referencedField = $pivotSchema->getPivotLeftIdField();
                }

                /** @var PivotPositionField $positionField */
                $positionField = $pivotSchema->getOnePositionField();

                $positionGetter = $positionField->getGetterForPrimitiveValue();
                $positionSetter = $positionField->getSetterForPrimitiveValue();
                $referencedGetter = $referencedField->getGetterForPrimitiveValue();
                $referencedSetter = $referencedField->getSetterForPrimitiveValue();
                $pointingSetter = $pointingField->getSetterForPrimitiveValue();

                $results = $this->_getPivots($ownField->getName());

                $checkedIds = [];

                // Update existing pivots
                foreach ($results as $result) {
                    $id = $result->{$referencedGetter}();
                    $updatedPosition = array_search($id, $ids);
                    $checkedIds[] = $id;

                    $position = $result->{$positionGetter}();

                    if ($updatedPosition !== $position) {
                        $result
                            ->{$positionSetter}($updatedPosition)
                            ->save();
                    }

                    // Unlink pivot relation
                    if (!in_array($id, $ids, true)) {
                        $result->delete();
                    }
                }

                // Link new pivot relations
                foreach ($ids as $i => $id) {
                    if (!in_array($id, $checkedIds, true)) {
                        $ins = $pivotSchema->getItemInstance();
                        $ins
                            ->{$pointingSetter}($this->getIdColumnValue())
                            ->{$referencedSetter}($id)
                            ->{$positionSetter}($i)
                            ->save();
                    }
                }
            }
        }

        if (count($this->PENDING_PIVOT_LINKS) > 0) {
            foreach ($this->PENDING_PIVOT_LINKS as $field => $relatedId) {
                $this->_addPivotRelation($field, $relatedId);
            }
        }

        if (count($this->PENDING_PARENT_FOREIGN_KEYS) > 0) {
            foreach ($this->PENDING_PARENT_FOREIGN_KEYS as $field => $relatedId) {
                if (is_array($relatedId)) {
                    foreach ($relatedId as $value) {
                        if ($value !== null) $this->_saveAppendToParentForeignKeys($field, $value);
                    }
                } else {
                    $this->_saveAppendToParentForeignKeys($field, $relatedId);
                }
            }
        }

        $this->_saveCompositionValues($isUpdate);

        if (isset($this->accessPolicy) && $this->accessPolicy->matchedEndOfLife(AccessPolicyEndOfLife::UntilNextWrite)) {
            unset($this->accessPolicy);
        }

        if ($reload) {
            $cacheCode = $schema->getInstanceCode($original);
            InstanceCache::clearCode($cacheCode);
            return Instantiator::make(static::COMPONENT, $this->getIdColumnValue(), $original);
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
}