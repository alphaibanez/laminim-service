<?php

namespace Lkt\Factory\Instance\Interfaces;

use Lkt\Factory\Schemas\ValueObjects\AccessPolicyUsage;

interface Item
{
    public function clearIdentifierValue(): static;
    public function getIdentifierValue(): array;
    public function isSameIdentifierValue(array $values): bool;
    public function isAnonymous(): bool;

    public function getAccessPolicyUsage(): AccessPolicyUsage|null;
}