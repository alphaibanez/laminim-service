<?php

namespace Lkt\Factory\Instantiator\Instances\AccessDataTraits;

use Lkt\Debug\VarDumper;
use Lkt\Factory\Instantiator\Conversions\RawResultsToInstanceConverter;
use Lkt\Factory\Instantiator\Helpers\UpdatedRelatedDataProcessor;
use Lkt\Factory\Instantiator\Instances\AbstractInstance;
use Lkt\Factory\Instantiator\Instantiator;
use Lkt\Factory\Schemas\Exceptions\InvalidComponentException;
use Lkt\Factory\Schemas\Exceptions\InvalidSchemaAppClassException;
use Lkt\Factory\Schemas\Exceptions\SchemaNotDefinedException;
use Lkt\Factory\Schemas\Fields\ForeignKeysField;
use Lkt\Factory\Schemas\Schema;

trait ColumnForeignListTrait
{
    /**
     * @param string $fieldName
     * @return array
     * @throws InvalidComponentException
     * @throws SchemaNotDefinedException
     */
    protected function _getForeignListIds(string $fieldName): array
    {
        $schema = Schema::get(static::COMPONENT);

        /** @var ForeignKeysField $field */
        $field = $schema->getField($fieldName);
        $allowAnonymous = $field->anonymousAllowed();

        $items = explode(';', trim($this->_getForeignListVal($fieldName)));
        $items = array_filter($items, function ($item) use ($allowAnonymous) {
            $t = trim($item);
            if ($t === '') {
                return false;
            }
            if ($allowAnonymous) {
                return true;
            }
            return (int)$t > 0;
        });

        return array_values($items);
    }

    /**
     * @param string $fieldName
     * @return array
     * @throws InvalidComponentException
     * @throws SchemaNotDefinedException
     * @throws InvalidSchemaAppClassException
     */
    protected function _getForeignListData(string $fieldName): array
    {
        $schema = Schema::get(static::COMPONENT);

        /** @var ForeignKeysField $field */
        $field = $schema->getField($fieldName);


        $ids = $this->_getForeignListIds($fieldName);

        $r = [];

        $idColumn = $schema->getIdColumn();
        $idColumn = reset($idColumn);


        $type = $field->getComponent();
        $dynamicComponentFieldName = $field->getDynamicComponentField();
        if ($dynamicComponentFieldName !== '') {
            $dynamicComponentField = $schema->getField($dynamicComponentFieldName);
            $getter = $dynamicComponentField->getGetterForPrimitiveValue();
            $dynamicType = $this->{$getter}();
            if ($dynamicType !== '') $type = $dynamicType;
        }

        if ($type === '') return [];

        $relatedSchema = Schema::get($type);
        $relatedClass = $relatedSchema->getInstanceSettings()->getAppClass();

        foreach ($ids as $id) {
            if (is_numeric($id)) {
                if ($relatedClass) {
                    $instance = call_user_func_array([$relatedClass, 'getInstance'], ['id' => $id]);
                    if ($instance instanceof AbstractInstance && !$instance->isAnonymous()) {
                        $r[] = $instance;
                    }

                } else {
                    $t = Instantiator::make($type, $id);
                    if ($t instanceof AbstractInstance && !$t->isAnonymous()) {
                        $r[] = $t;
                    }
                }

            } else {
                $t = Instantiator::make($type, null);
                $t->setData([
                    $idColumn => $id,
                ]);
                $r[] = $t;
            }
        }

        return $r;
    }

    /**
     * @param string $fieldName
     * @return string
     */
    protected function _getForeignListVal(string $fieldName): string
    {
        if (isset($this->UPDATED[$fieldName])) {
            return $this->UPDATED[$fieldName];
        }
        return trim($this->DATA[$fieldName]);
    }

    /**
     * @param string $fieldName
     * @return bool
     */
    protected function _hasForeignListVal(string $fieldName): bool
    {
        $checkField = 'has' . ucfirst($fieldName);
        if (isset($this->UPDATED[$checkField])) {
            return $this->UPDATED[$checkField];
        }
        return $this->DATA[$checkField] === true;
    }

    /**
     * @param string $fieldName
     * @param string|array|null $value
     * @throws InvalidComponentException
     * @throws SchemaNotDefinedException
     */
    protected function _setForeignListVal(string $fieldName, $value = null): static
    {
        if (is_array($value)) {
            $value = implode(';', $value);
        } elseif (!is_string($value)) {
            $value = trim($value);
        }
        $converter = new RawResultsToInstanceConverter(static::COMPONENT, [
            $fieldName => $value,
        ], false);

        $this->UPDATED = $this->UPDATED + $converter->parse();
        return $this;
    }

    protected function _setForeignListWithData(string $fieldName, array $data = []): static
    {
        $schema = Schema::get(static::COMPONENT);
        $accessPolicy = 'lkt-related';
        $field = $schema->getField($fieldName);
        if ($this->accessPolicy) {
            $auxAccessPolicy = $field->getAssociatedAccessPolicy($this->accessPolicy->name);
            if ($auxAccessPolicy) $accessPolicy = $auxAccessPolicy;
        }

        $dataProcessor = new UpdatedRelatedDataProcessor(
            $schema,
            $fieldName,
            $data,
            $this,
            $accessPolicy
        );
        $dataProcessor->processRelatedField();

        if (count($dataProcessor->pendingUpdateData) > 0) {
            $this->PENDING_UPDATE_RELATED_DATA[$fieldName] = $dataProcessor->pendingUpdateData;
        }
        if (count($dataProcessor->updatedData) > 0) {
            $this->UPDATED_RELATED_DATA[$fieldName] = $dataProcessor->updatedData;
        }

        if (count($dataProcessor->relatedIds) > 0) {
            $this->_setForeignListVal($fieldName, $dataProcessor->relatedIds);

        } elseif (count($data) === 0 && count($this->_getForeignListIds($fieldName)) > 0) {
            $this->_setForeignListVal($fieldName, []);
        }
        return $this;
    }

    /**
     * @param string $fieldName
     * @param array $value
     * @return $this
     * @throws InvalidComponentException
     * @throws SchemaNotDefinedException
     */
    protected function _removeForeignListIds(string $fieldName, array $value = []): static
    {
        $r = [];
        $current = $this->_getForeignListIds($fieldName);
        foreach ($current as $val) if (!in_array($val, $value)) $r[] = $val;

        $converter = new RawResultsToInstanceConverter(static::COMPONENT, [
            $fieldName => implode(';', $r),
        ], false);

        foreach ($converter->parse() as $key => $value) {
            $this->UPDATED[$key] = $value;
        }
        return $this;
    }
}