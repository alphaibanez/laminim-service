<?php

namespace Lkt\Factory\Instance\DataControllers;

use Lkt\Debug\VarDumper;
use Lkt\Factory\Instance\Enums\EmptyDataMode;
use Lkt\Factory\Instance\Enums\InvalidDataMode;
use Lkt\Factory\Instance\Enums\TrimMode;
use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Schemas\Exceptions\DuplicatedValueException;
use Lkt\Factory\Schemas\Exceptions\InvalidItemDataAssignException;
use Lkt\Factory\Schemas\Schema;

final class StringDataController
{
    private array $data = [];
    private array $payload = [];

    private Schema $schema;
    private Item $item;

    public function __construct(Schema &$schema, Item &$ins, array $data)
    {
        $this->schema = $schema;
        $this->item = $ins;
        foreach ($data as $k => $datum) $this->setOriginal($k, $datum);
    }

    public function get(string $key): string|null
    {
        if (array_key_exists($key, $this->payload)) {
            return $this->payload[$key];
        }

        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }

        return null;
    }

    public function has(string $key, EmptyDataMode $mode = EmptyDataMode::NullAndEmpty): bool
    {
        $v = $this->get($key);

        if ($mode === EmptyDataMode::OnlyNull) return $v !== null;
        return $v !== '';
    }

    public function set(string $key, $value): self
    {
        $f = $this->schema->getStringField($key);
        if (!$f) {
            throw InvalidItemDataAssignException::missingField($key);
        }

        if (is_object($f) && method_exists($f, 'isUnique') && $f->isUnique()) {
            $setter = 'and' . ucfirst($key) . 'Equal';
            $builder = $this->schema->getQueryBuilder()->{$setter}($value);
            $result = $this->schema->getOne($builder);
            if ($result instanceof Item && $result->isSameIdentifierValue($this->item->getIdentifierValue())) {
                throw DuplicatedValueException::getInstance($value);
            }
        }

        $currentValue = $this->get($key);
        $parsedValue = $this->parse($key, $value, $f->getInvalidDataMode());

        if ($parsedValue !== $currentValue) {
            $this->payload[$key] = $parsedValue;
        }

        return $this;
    }

    public function parse(string $key, $value, InvalidDataMode $mode = InvalidDataMode::CastToType): string|null
    {
        if ($value === null) return null;

        $f = $this->schema->getStringField($key);
        $trimMode = $f->getTrimMode();

        if (is_string($value)) {
            return $this->trim($value, $trimMode);
        }

        return match ($mode) {
            InvalidDataMode::CastToType => $this->trim((string)$value, $trimMode),
            InvalidDataMode::CastToEmpty => '',
            default => null,
        };
    }

    private function trim(string $value, TrimMode $mode): string
    {
        return match ($mode) {
            TrimMode::Full => trim($value),
            TrimMode::Start => ltrim($value),
            TrimMode::End => rtrim($value),
            default => $value,
        };
    }

    public function getOriginal(string $key): string|null
    {
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }

        return null;
    }

    public function setOriginal(string $key, $value): self
    {
        $f = $this->schema->getStringField($key);
        $parsedValue = $this->parse($key, $value, $f->getInvalidDataMode());
        $this->data[$key] = $parsedValue;
        return $this;
    }

    public function dumpPayloadIntoOriginal(): self
    {
        $this->data = [...$this->data, ... $this->payload];
        return $this;
    }
}