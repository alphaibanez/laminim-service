<?php

namespace Lkt\Factory\Instantiator\Instances\AccessDataTraits;

use Lkt\Factory\Instance\Traits\ItemWithRelatedItemsDataTrait;
use Lkt\Factory\Schemas\Exceptions\InvalidComponentException;
use Lkt\Factory\Schemas\Exceptions\InvalidSchemaAppClassException;
use Lkt\Factory\Schemas\Exceptions\SchemaNotDefinedException;
use Lkt\Factory\Schemas\Fields\RelatedKeysField;
use Lkt\Factory\Schemas\Schema;
use Lkt\QueryBuilding\Query;

trait ColumnRelatedKeysTrait
{
    use ItemWithRelatedItemsDataTrait;

    protected array $PENDING_PARENT_FOREIGN_KEYS = [];

    /**
     * @param $type
     * @param $column
     * @param $forceRefresh
     * @return array
     * @throws InvalidComponentException
     * @throws InvalidSchemaAppClassException
     * @throws SchemaNotDefinedException
     */
    protected function _getRelatedKeysVal($type = '', $column = '', $forceRefresh = false): array
    {
        return $this->relatedItemsData->getItems($type, null, null, null, [], $forceRefresh);
    }

    protected function _getRelatedKeysIds(string $fieldName): array
    {
        return $this->relatedItemsData->getItemsIds($fieldName, null, null, null, [], false);
    }

    /**
     * @param $type
     * @param $column
     * @param $forceRefresh
     * @return Query|null
     * @throws InvalidComponentException
     * @throws SchemaNotDefinedException
     */
    protected function _getRelatedKeysQueryBuilder($type = '', $column = '', $forceRefresh = false)
    {
        return $this->relatedItemsData->getQuery($column, null, null, null, [], $forceRefresh);
    }

    /**
     * @param $type
     * @param $column
     * @param $forceRefresh
     * @return Query|null
     * @throws InvalidComponentException
     * @throws SchemaNotDefinedException
     */
    protected function _getRelatedKeysQueryCaller($type = '', $column = '', $forceRefresh = false)
    {
        return $this->relatedItemsData->getQuery($column, null, null, null, [], $forceRefresh);
    }

    /**
     * @param $type
     * @param $column
     * @return bool
     * @throws InvalidComponentException
     * @throws InvalidSchemaAppClassException
     * @throws SchemaNotDefinedException
     */
    protected function _hasRelatedKeysVal($type = '', $column = ''): bool
    {
        return $this->relatedItemsData->has($type);
    }

    protected function _setRelatedKeysValWithData($type = '', $column = '', $data = [])
    {
        $this->PENDING_UPDATE_RELATED_DATA[$column] = $data;

        $schema = Schema::get($type);
        /** @var RelatedKeysField $field */
        $field = $schema->getField($column);

        $relatedSchema = Schema::get($field->getComponent());

        $relatedIdColumn = $relatedSchema->getIdColumn()[0];
        $relatedClass = $relatedSchema->getInstanceSettings()->getAppClass();

        $r = [];

        foreach ($data as $datum) {
            $instance = $relatedClass::getInstance($datum[$relatedIdColumn]);
//            $instance::feedInstance($instance, $datum);
            $instance->feed($datum);
            $r[] = $instance;
        }
        return $this;
    }

    protected function _appendToParentForeignKeys(string $field, int|array $parentValue): static
    {
        $this->PENDING_PARENT_FOREIGN_KEYS[$field] = $parentValue;
        return $this;
    }

    protected function _saveAppendToParentForeignKeys(string $fieldName, int|array $parentValue): static
    {
        $schema = Schema::get(static::COMPONENT);
        $field = $schema->getField($fieldName);

        if ($field instanceof RelatedKeysField) {
            $relatedSchema = Schema::get($field->getComponent());
            $relatedSchemaField = $relatedSchema->getField($field->getColumn());

            $setter = $relatedSchemaField->getSetter();
            $getter = $relatedSchemaField->getGetterForPrimitiveValue();

            if (!is_array($parentValue)) $parentValue = [$parentValue];
            foreach ($parentValue as $value) {
                $instance = $relatedSchema->getItemInstance($value);
                if (!$instance->isAnonymous()) {
                    $currentIds = $instance->{$getter}();
                    if (!in_array($this->getIdColumnValue(), $currentIds)) {
                        $instance->{$setter}([
                            ...$currentIds,
                            $this->getIdColumnValue(),
                        ])->save();
                    }
                }
            }
        }
        return $this;
    }
}