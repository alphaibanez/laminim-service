<?php

namespace Lkt\Factory\Instance\DataControllers;

use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Instantiator\Helpers\QueryBuilderHelper;
use Lkt\Factory\Instantiator\Helpers\UpdatedRelatedDataProcessor;
use Lkt\Factory\Instantiator\Instantiator;
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

    private Schema $schema;
    private Item $item;

    public function __construct(Schema &$schema, Item &$ins, array $data)
    {
        $this->schema = $schema;
        $this->item = $ins;
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
        if (!$forceRefresh) {
            if (array_key_exists($key, $this->payload)) return $this->payload[$key];
            if (array_key_exists($key, $this->data)) return $this->data[$key];
        }

        $field = $this->schema->getRelatedField($key);
        if (!$field) return null;

        $builder = QueryBuilderHelper::prepareRelatedQuery(
            $this->item,
            QueryBuilderHelper::getComponentQuery($field->getComponent()),
            $this->schema,
            $field,
            $forceRefresh,
            $additionalData,
        );

        if (is_numeric($page)) {
            $limit = ($itemsPerPage ?? $field->getItemsPerPage()) ?? 10;
            $builder->pagination($page, $limit);
        }

        if ($where instanceof Where) {
            $builder->andWhere($where);
        }

        $data = $builder->select();
        $relatedSchema = Schema::get($field->getComponent());

        $results = Instantiator::makeResults($relatedSchema->getComponent(), $data);
        if (count($results) > 0) {
            $this->data[$key] = $results;
            return $this->data[$key];
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

        $field = $this->schema->getRelatedField($key);
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

        $field = $this->schema->getRelatedField($key);
        if (!$field) return null;

        $total = $this->getItemsCount($key, $where, $countableField);
        $limit = ($itemsPerPage ?? $field->getItemsPerPage()) ?? 10;

        $this->itemsAmountOfPages[$key] = getTotalPages($total, $limit);
        return $this->itemsAmountOfPages[$key];
    }

    public function has(string $key): bool
    {
        $v = $this->getItems($key);
        return is_array($v) && count($v) > 0;
    }

    public function __debugInfo()
    {
        return [
            'data' => $this->data,
        ];
    }

    public function setItems(string $key, array $items, string $accessPolicy = 'lkt-related')
    {
        $field = $this->schema->getRelatedField($key);
        if (!$field) return null;

        $accessPolicyUsage = $this->item->getAccessPolicyUsage();

        if ($accessPolicyUsage) {
            $customRelationAccessPolicy = $field->getAssociatedAccessPolicy($accessPolicyUsage->name);
            if ($customRelationAccessPolicy) $accessPolicy = $customRelationAccessPolicy;
        }

        $dataProcessor = new UpdatedRelatedDataProcessor(
            $this->schema,
            $key,
            $items,
            $this->item,
            $accessPolicy
        );
        $dataProcessor->processRelatedField();

        $this->needsUpdate[$key] = $dataProcessor->pendingUpdateData;
        $this->payload[$key] = $dataProcessor->updatedData;
    }
}