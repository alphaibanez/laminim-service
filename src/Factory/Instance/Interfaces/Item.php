<?php

namespace Lkt\Factory\Instance\Interfaces;

use Lkt\Factory\Schemas\Schema;
use Lkt\Factory\Schemas\ValueObjects\AccessPolicyUsage;

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
    public function isSameIdentifierValue(array $values): bool;
    public function isAnonymous(): bool;

    /**
     * @laminim
     * Access policies methods
     */
    public function getAccessPolicyUsage(): AccessPolicyUsage|null;

    /**
     * @laminim
     * Data handling methods
     */
    public function feed(array $data, array $internalMethodsArguments = []): static;
    public function feedAndSave(array $data, array $internalMethodsArguments = []): static;
    public function save(): static;

    /**
     * @laminim
     * Dynamic method calling
     */
    public function prepareOwnMethodCallArguments(string $method, array $args, string $fieldName): array;
    public function satisfiedOwnMethodCallArguments(string $method, array $args): bool;
    public function callOwnMethod(string $method, array $args): mixed;
}