<?php

namespace Lkt\Factory\Instance\Traits;

use Lkt\Factory\Schemas\Schema;

trait ItemWithIdentifierValueTrait
{
    private array|null $identifierValue = null;

    public function clearIdentifierValue(): static
    {
        $this->identifierValue = null;
        return $this;
    }

    public function getIdentifierValue(): array
    {
        if (is_array($this->identifierValue)) return $this->identifierValue;

        $schema = Schema::get(static::COMPONENT);
        $identifiers = $schema->getIdentifiers();
        $r = [];
        foreach ($identifiers as $identifier) {
            $k = $identifier->getName();
            $r[$k] = $this->DATA[$k];
        }

        ksort($r);
        $this->identifierValue = $r;
        return $this->identifierValue;
    }

    public function isSameIdentifierValue(array $values): bool
    {
        $ownValue = $this->getIdentifierValue();
        $diff = array_diff($ownValue, $values);
        return count($diff) === 0;
    }

    public function isAnonymous(): bool
    {
        return count($this->DATA) === 0;
    }
}