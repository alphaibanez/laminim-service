<?php

namespace Lkt\Factory\Instance\Interfaces;

use Lkt\Factory\Instance\Enums\RetrieveDataMode;
use Lkt\Factory\Instantiator\Instances\BatchActions;
use Lkt\Factory\Schemas\Enums\AccessPolicyEndOfLife;
use Lkt\Factory\Schemas\Schema;
use Lkt\Factory\Schemas\ValueObjects\AccessPolicy;
use Lkt\Factory\Schemas\ValueObjects\AccessPolicyUsage;
use Lkt\QueryBuilding\Query;

interface Item
{
    /**
     * @laminim
     * Self schema reference
     */
    public function getSchema(): Schema|null;

    /**
     * @laminim
     * Item identification methods
     */
    public function clearIdentifierValue(): static;
    public function getIdentifierValue(): array;
    public function getIdColumnValue(): mixed;
    public function isSameIdentifierValue(array $values): bool;
    public function isAnonymous(): bool;

    /**
     * @laminim
     * Access policies methods
     */
    public function setAccessPolicy(string|AccessPolicy $accessPolicy, AccessPolicyEndOfLife $accessPolicyEndOfLife = AccessPolicyEndOfLife::UntilUpdated): static;
    public function getAccessPolicyUsage(): AccessPolicyUsage|null;

    /**
     * @laminim
     * Data handling methods
     */
    public function initialFeed(array $initialData = [], bool $refreshing = false): static;
    public function feed(array $data, array $internalMethodsArguments = []): static;
    public function feedAndSave(array $data, array $internalMethodsArguments = []): static;
    public function save(): static;

    public function assignValue(string $key, mixed $value): static;
    public function retrieveValue(string $key, array $additionalData = [], RetrieveDataMode $dataMode = RetrieveDataMode::Raw): mixed;
    public function readValue(string $key, string $responseKey, array $additionalData = []): array|null;
    public function hasAssignedValue(string $key, array $additionalData = []): bool;
    public function getOriginalData(): array;
    public function getUpdatePayload(): array;

    /**
     * @laminim
     * CRUD oriented methods
     */
    public function autoRead(array $internalMethodsArguments = []): array;
    public function readFields(array $fields = [], array $internalMethodsArguments = []): array;
    public function duplicate(): static;
    public function saveDuplicate(): static;
    public function delete(): static;
    public static function getBatchActions(array $items): BatchActions;

    /**
     * @laminim
     * Dynamic method calling
     */
    public function prepareOwnMethodCallArguments(string $method, array $args, string $fieldName): array;
    public function satisfiedOwnMethodCallArguments(string $method, array $args): bool;
    public function callOwnMethod(string $method, array $args): mixed;

    /**
     * @laminim
     * Instance factory
     */
    public static function getInstance($id = null, array $initialData = []): static;
    public static function getInstanceOrNull($id = null, array $initialData = []): static|null;
    public static function getMany(Query $queryCaller = null): array;
    public static function getPage(int $page, Query $queryCaller = null, int $itemsPerPage = 0): array;
    public static function getOne(Query $queryCaller = null);
    public static function getCount(Query $queryCaller = null, string $countableField = null): int;
    public static function getAmountOfPages(Query $queryCaller = null, string $countableField = null, int $itemsPerPage = 0): int;
}