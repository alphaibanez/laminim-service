<?php

namespace Lkt\Factory\Instance\Traits;

use Lkt\Connectors\Cache\QueryCache;
use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Instantiator\Cache\InstanceCache;
use Lkt\Factory\Instantiator\Enums\CrudOperation;
use Lkt\Factory\Instantiator\Instances\AbstractInstance;
use Lkt\Factory\Instantiator\Instances\BatchActions;
use Lkt\Factory\Instantiator\ValueObjects\ComponentDatabaseIntegration;
use Lkt\Factory\Schemas\Enums\AccessPolicyEndOfLife;
use Lkt\Factory\Schemas\Enums\RelatedFieldClonePolicy;
use Lkt\Factory\Schemas\Fields\AbstractField;
use Lkt\Factory\Schemas\Fields\ForeignKeyField;
use Lkt\Factory\Schemas\Fields\ForeignKeysField;
use Lkt\Factory\Schemas\Fields\JSONField;
use Lkt\Factory\Schemas\Fields\RelatedKeysField;
use Lkt\Factory\Schemas\Schema;
use Lkt\Locale\Locale;
use Lkt\Translations\Translations;

trait ItemWithCrudTrait
{

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
     * @param AbstractField[] $fields
     * @return array
     */
    public function readFields(array $fields = [], array $internalMethodsArguments = []): array
    {
        $schema = Schema::get(static::COMPONENT);
        $r = [];
        foreach ($fields as $key => $field) {
            $responseKey = $key ?? $field->getName();

            $composedDatum = !$schema->hasFieldDefined($field->getName());

            // Composed related data
            if ($composedDatum) {

                $fieldComposingThisField = $schema->getCompositionFieldComposingThisField($field->getName());
                if (!$fieldComposingThisField) continue;

                $composedInstance = $this->composedData->getItem($fieldComposingThisField->getName(), $internalMethodsArguments);

                if (!$composedInstance) continue;
                $dataToAdd = $composedInstance->readValue($field->getName(), $responseKey);
                foreach ($dataToAdd as $z => $y) $r[$z] = $y;
                continue;
            }

            $dataToAdd = $this->readValue($field->getName(), $responseKey);
            foreach ($dataToAdd as $z => $y) $r[$z] = $y;
        }

        if (method_exists($this, 'postProcessRead')) return $this->postProcessRead($r);
        ksort($r);
        return $r;
    }

    public function duplicate(): static
    {
        /** @var Item $clone */
        $clone = static::getInstance();
        $clone->setAccessPolicy('duplicate');
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

        $clone->feed($payload);
//        static::feedInstance($clone, $payload);
        return $clone;
    }

    public function saveDuplicate(): static
    {
        return $this->duplicate()->save();
    }

    public static function getBatchActions(array $items): BatchActions
    {
        return BatchActions::fromComponent(static::COMPONENT, $items);
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
        $this->initialFeed([]);
        return $this;
    }

    public static function mkOrUp(array $data): static
    {
        $instance = static::getOne(static::getUniqueFilteredQueryBuilder($data));
        if (!$instance) {
            $instance = static::getInstance()->feedAndSave($data);
        } else {
            $instance->feedAndSave($data);
        }
        return $instance;
    }

    public static function mkIfNot(array $data): static
    {
        $instance = static::getOne(static::getUniqueFilteredQueryBuilder($data));
        if (!$instance) {
            $instance = static::getInstance()->feedAndSave($data);
        }
        return $instance;
    }
}