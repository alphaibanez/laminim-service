<?php

namespace Lkt\Factory\Instance\Interfaces;

interface Item
{
    public function clearIdentifierValue(): static;
    public function getIdentifierValue(): array;
    public function isSameIdentifierValue(array $values): bool;
    public function isAnonymous(): bool;
}