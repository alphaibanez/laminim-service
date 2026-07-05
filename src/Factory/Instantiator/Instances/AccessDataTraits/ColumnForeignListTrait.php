<?php

namespace Lkt\Factory\Instantiator\Instances\AccessDataTraits;

use Lkt\Debug\VarDumper;
use Lkt\Factory\Instance\Traits\ItemWithForeignKeysDataTrait;
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
    use ItemWithForeignKeysDataTrait;

    /**
     * @param string $fieldName
     * @return array
     * @throws InvalidComponentException
     * @throws SchemaNotDefinedException
     */
    protected function _getForeignListIds(string $fieldName): array
    {
        return $this->foreignKeysData->getIds($fieldName) ?? [];
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
        return $this->foreignKeysData->getItems($fieldName) ?? [];
    }

    /**
     * @param string $fieldName
     * @return string
     */
    protected function _getForeignListVal(string $fieldName): string
    {
        return $this->foreignKeysData->get($fieldName);
    }

    /**
     * @param string $fieldName
     * @return bool
     */
    protected function _hasForeignListVal(string $fieldName): bool
    {
        return $this->foreignKeysData->has($fieldName);
    }

    /**
     * @param string $fieldName
     * @param string|array|null $value
     * @throws InvalidComponentException
     * @throws SchemaNotDefinedException
     */
    protected function _setForeignListVal(string $fieldName, $value = null): static
    {
        $this->foreignKeysData->set($fieldName, $value);
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
            if ($this->DATA[$key] !== $value) $this->UPDATED[$key] = $value;
        }
        return $this;
    }
}