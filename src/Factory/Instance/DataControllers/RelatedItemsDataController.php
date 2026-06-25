<?php

namespace Lkt\Factory\Instance\DataControllers;

use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Instantiator\Helpers\QueryBuilderHelper;
use Lkt\Factory\Instantiator\Instantiator;
use Lkt\Factory\Schemas\Fields\RelatedKeysField;
use Lkt\Factory\Schemas\Schema;
use Lkt\QueryBuilding\Where;
use function Lkt\Tools\Pagination\getTotalPages;

final class RelatedItemsDataController
{
    private array $data = [];
    private array $payload = [];
    private array $needsUpdate = [];
    private array $itemsCount = [];
    private array $itemsAmountOfPages = [];

    private array $appendItemsInParentForeignKeysFieldStack = [];

    private Schema $schema;
    private Item $item;

    public function __construct(Schema &$schema, Item &$ins, array $data)
    {
        $this->schema = $schema;
        $this->item = $ins;
    }

    public function has(string $key): bool
    {
        $v = $this->getItems($key);
        return is_array($v) && count($v) > 0;
    }

    public function getItemsIdentifiers(
        string     $key,
        Where|null $where = null,
        int|null   $page = null,
        int|null   $itemsPerPage = null,
        array      $additionalData = [],
        bool       $forceRefresh = false
    ): array|null
    {
        $items = $this->getItems($key, $where, $page, $itemsPerPage, $additionalData, $forceRefresh);

        return array_map(function (Item $item){
            return $item->getIdentifierValue();
        }, $items);
    }

    /**
     * @param string $key
     * @param Where|null $where
     * @param int|null $page
     * @param int|null $itemsPerPage
     * @param array $additionalData
     * @param bool $forceRefresh
     * @return Item[]|null
     * @throws \Lkt\Factory\Schemas\Exceptions\InvalidComponentException
     * @throws \Lkt\Factory\Schemas\Exceptions\InvalidSchemaAppClassException
     * @throws \Lkt\Factory\Schemas\Exceptions\SchemaNotDefinedException
     */
    public function getItems(
        string     $key,
        Where|null $where = null,
        int|null   $page = null,
        int|null   $itemsPerPage = null,
        array      $additionalData = [],
        bool       $forceRefresh = false
    ): array|null
    {
        $cacheKey = [$key];
        if ($page !== null) $cacheKey[] = $page;
        $cacheKey = implode('-', $cacheKey);

        if (!$forceRefresh) {
            if (array_key_exists($cacheKey, $this->payload)) return $this->payload[$cacheKey];
            if (array_key_exists($cacheKey, $this->data)) return $this->data[$cacheKey];
        }

        $field = $this->schema->getKindOfRelatedField($key);
        if (!$field) return null;

        $builder = QueryBuilderHelper::prepareRelatedQuery(
            $this->item,
            QueryBuilderHelper::getComponentQuery($field->getComponent()),
            $this->schema,
            $field,
            $forceRefresh,
            $additionalData,
        );

        if ($field instanceof RelatedKeysField) {
            $constraints = $this->item::getWhereBuilder();
            foreach ($this->item->getIdentifierValue() as $column => $value) {
                $constraints->andWhere(
                    $this->item::getWhereBuilder()
                    ->orStringLike($column, ";{$value};")
                    ->orStringLike($column, "{$value}")
                    ->orStringEndsLike($column, "{$value};")
                    ->orStringBeginsLike($column, ";{$value}")
                );
            }
            $builder->andWhere($constraints);
        }

        if (is_numeric($page)) {
            $limit = ($itemsPerPage ?? $field->getItemsPerPage()) ?? 10;
            $builder->pagination($page, $limit);
        }

        $fieldConfigWhere = $field->getWhere();
        if ($fieldConfigWhere) $builder->andWhere($fieldConfigWhere);

        if ($where instanceof Where) {
            $builder->andWhere($where);
        }

        $data = $builder->select();
        $relatedSchema = Schema::get($field->getComponent());

        $results = Instantiator::makeResults($relatedSchema->getComponent(), $data);
        if (count($results) > 0) {
            $this->data[$cacheKey] = $results;
            return $this->data[$cacheKey];
        }
        return null;
    }

    public function getItemsCount(
        string      $key,
        Where|null  $where = null,
        string|null $countableField = null
    ): int|null
    {
        if (array_key_exists($key, $this->itemsCount)) return $this->itemsCount[$key];

        $field = $this->schema->getKindOfRelatedField($key);
        if (!$field) return null;

        if (!$countableField) {
            $countableField = $field->getCountableField();
        }

        if (!$countableField) {
            $relatedSchema = Schema::get($field->getComponent());
            $identifiers = $relatedSchema->getIdentifiers();
            if (count($identifiers) === 1) {
                $countableField = $identifiers[0]->getColumn();
            }
        }

        $builder = QueryBuilderHelper::prepareRelatedQuery(
            $this->item,
            QueryBuilderHelper::getComponentQuery($field->getComponent()),
            $this->schema,
            $field,
        );

        if ($where instanceof Where) {
            $builder->andWhere($where);
        }

        $this->itemsCount[$key] = $builder->count($countableField);
        return $this->itemsCount[$key];
    }

    public function getItemsAmountOfPages(
        string      $key,
        Where|null  $where = null,
        string|null $countableField = null,
        int|null    $itemsPerPage = null
    ): int|null
    {
        if (array_key_exists($key, $this->itemsAmountOfPages)) return $this->itemsAmountOfPages[$key];

        $field = $this->schema->getKindOfRelatedField($key);
        if (!$field) return null;

        $total = $this->getItemsCount($key, $where, $countableField);
        $limit = ($itemsPerPage ?? $field->getItemsPerPage()) ?? 10;

        $this->itemsAmountOfPages[$key] = getTotalPages($total, $limit);
        return $this->itemsAmountOfPages[$key];
    }

    public function __debugInfo()
    {
        return [
            'data' => $this->data,
        ];
    }

    public function setItems(string $key, array $items, string $accessPolicy = 'lkt-related')
    {
        $field = $this->schema->getKindOfRelatedField($key);
        if (!$field) return null;

        $accessPolicyUsage = $this->item->getAccessPolicyUsage();

        if ($accessPolicyUsage) {
            $customRelationAccessPolicy = $field->getAssociatedAccessPolicy($accessPolicyUsage->name);
            if ($customRelationAccessPolicy) $accessPolicy = $customRelationAccessPolicy;
        }

        $relatedComponent = $field->getComponent($this->schema, $this->item);
        if ($relatedComponent === '') return null;

        $relatedSchema = Schema::get($relatedComponent);
        $relatedClass = $relatedSchema->getInstanceSettings()->getAppClass();
        $relatedIdentifiers = $relatedSchema->getIdentifiers();

        $itemsWithFedData = [];
        foreach ($this->data as &$datum) {
            if (is_array($datum)) {

                $constructorData = [];

                foreach ($relatedIdentifiers as $relatedIdentifier) {
                    $name = $relatedIdentifier->getName();
                    // @todo detect $this->item applied access policy
                    // and change the name var in order of avoid missing refs
                    // in case a field name was overwritten
                    if (!$datum[$name]) {
                        if (method_exists($field, 'getRelatedComponentFeeds')){
                            foreach ($field->getRelatedComponentFeeds() as $relatedColumnKey => $relatedColumnValue) {
                                if (is_callable($relatedColumnValue)) {
                                    $relatedColumnValue = call_user_func_array($relatedColumnValue, [
                                        'referrer' => $this->item
                                    ]);
                                }
                                if (!$datum[$relatedColumnKey]) $datum[$relatedColumnKey] = $relatedColumnValue;
                            }
                        }
                    }

                    $constructorData[$name] = $datum[$name];
                }

                /** @var Item $instance */
                $instance = call_user_func_array([$relatedClass, 'getInstance'], $constructorData);
                if ($accessPolicy) $instance->setAccessPolicy($accessPolicy);
                $instance::feedInstance($instance, $datum);

            } else if (is_numeric($datum)) {
                $instance = call_user_func_array([$relatedClass, 'getInstance'], [$datum]);

            }

            if ($instance) {
                $itemsWithFedData[] = $instance;
            }
        }

        // @remember Changes won't be persistent until $this->item was saved!
        $this->needsUpdate[$key] = $itemsWithFedData;
        $this->payload[$key] = $items;
    }

    public function prepareToAppendItemsInParentForeignKeysField(string $key, int|array $parentIdentifierValue): self
    {
        $this->appendItemsInParentForeignKeysFieldStack[$key] = $parentIdentifierValue;
        return $this;
    }

    public function appendItemsInParentForeignKeysField(string $key): self
    {
        $field = $this->schema->getRelatedKeysField($key);
        if (!$field) return $this;


        $relatedId = $this->appendItemsInParentForeignKeysFieldStack[$key];
        if (!is_array($relatedId)) {
            $relatedId = [$relatedId];
        }

        // @todo

        return $this;
    }
}