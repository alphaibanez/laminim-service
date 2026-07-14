<?php

namespace Lkt\Factory\Instance\Traits;

use Lkt\Debug\VarDumper;
use Lkt\Factory\Schemas\Exceptions\InvalidComponentException;
use Lkt\Factory\Schemas\Exceptions\SchemaNotDefinedException;
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
        $data = $this->getOriginalData();
        foreach ($identifiers as $identifier) {
            $k = $identifier->getName();
            $r[$k] = $data[$k];
        }

        ksort($r);
        $this->identifierValue = $r;
        return $this->identifierValue;
    }

    /**
     * @return mixed
     * @throws InvalidComponentException
     * @throws SchemaNotDefinedException
     */
    public function getIdColumnValue(): mixed
    {
        $data = $this->getOriginalData();
        $schema = Schema::get(static::COMPONENT);
        $identifiers = $schema->getIdentifiers();
        if (count($identifiers) === 1) {
            $identifierValue = $this->getIdentifierValue();
            return $identifierValue[array_keys($identifierValue)[0]];
//            return $data[$schema->getIdString()];
        }

        $r = [];
        foreach ($identifiers as $identifier) {
            $k = $identifier->getName();
            $r[$k] = $data[$k];
        }

        return $r;
    }

    public function isSameIdentifierValue(array $values): bool
    {
        $ownValue = $this->getIdentifierValue();
        $diff = array_diff($ownValue, $values);
        return count($diff) === 0;
    }

    public function isAnonymous(): bool
    {
        return count($this->getOriginalData()) === 0;
    }
}