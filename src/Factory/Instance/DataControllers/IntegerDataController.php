<?php

namespace Lkt\Factory\Instance\DataControllers;

use Lkt\Debug\VarDumper;
use Lkt\Factory\Instance\Enums\EmptyDataMode;
use Lkt\Factory\Instance\Enums\InvalidDataMode;
use Lkt\Factory\Instance\Enums\TrimMode;
use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Instantiator\Exceptions\InvalidIntegerChoiceValueException;
use Lkt\Factory\Schemas\Exceptions\DuplicatedValueException;
use Lkt\Factory\Schemas\Exceptions\InvalidItemDataAssignException;
use Lkt\Factory\Schemas\Fields\IntegerChoiceField;
use Lkt\Factory\Schemas\Schema;

final class IntegerDataController
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

    public function get(string $key): int|null
    {
        if (array_key_exists($key, $this->payload)) {
            return $this->payload[$key];
        }

        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }

        return null;
    }

    public function has(string $key): bool
    {
        $v = $this->get($key);

        $f = $this->schema->getIntegerField($key);
        $mode = $f->getEmptyDataMode();

        if ($mode === EmptyDataMode::OnlyNull) return $v !== null;
        return $v > 0;
    }

    public function set(string $key, $value): self
    {
        $f = $this->schema->getIntegerField($key);
        if (!$f) {
            throw InvalidItemDataAssignException::missingField($key);
        }

        $currentValue = $this->get($key);
        $parsedValue = $this->parse($key, $value);

        if ($parsedValue !== $currentValue) {
            $this->payload[$key] = $parsedValue;
        }

        return $this;
    }

    public function parse(string $key, $value): int|null
    {
        if ($value === null) return null;

        $f = $this->schema->getIntegerField($key);
        $minValue = $f->getMinValue();

        if (is_int($value)) {
            if (is_int($minValue) && $value > $minValue) {
                $value = $minValue;
            }
            return $value;

        } else {
            $mode = $f->getInvalidDataMode();

            $value = match ($mode) {
                InvalidDataMode::CastToType => is_int($minValue) && (int)$value < $minValue ? $minValue : (int)$value,
                InvalidDataMode::CastToEmpty => 0,
                default => null,
            };
        }

        if ($f instanceof IntegerChoiceField) {
            $availableOptions = $f->getAllowedOptions();

            if (!in_array($value, $availableOptions, true)) {
                throw InvalidIntegerChoiceValueException::getInstance($value, $key, $this->schema->getComponent());
            }
        }

        return $value;
    }

    public function in(string $key, array $values): bool
    {
        return in_array($this->get($key), $values, true);
    }

    public function equal(string $key, int|object $compared): bool
    {
        $c = $compared;
        if (is_object($compared) && property_exists($compared, 'value') && isset($compared->value)) {
            $c = $compared->value;
        }

        return $this->get($key) === $c;
    }

    public function getOriginal(string $key): int|null
    {
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }

        return null;
    }

    public function setOriginal(string $key, $value): self
    {
        $parsedValue = $this->parse($key, $value);
        $this->data[$key] = $parsedValue;
        return $this;
    }

    public function dumpPayloadIntoOriginal(): self
    {
        $this->data = [...$this->data, ... $this->payload];
        return $this;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function getOriginalData(): array
    {
        return $this->data;
    }

    public function __debugInfo() {
        return [
            'data' => $this->data,
            'payload' => $this->payload,
        ];
    }
}