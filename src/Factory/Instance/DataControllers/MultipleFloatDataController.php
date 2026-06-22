<?php

namespace Lkt\Factory\Instance\DataControllers;

use Lkt\Factory\Instance\Enums\EmptyDataMode;
use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Schemas\Exceptions\InvalidItemDataAssignException;
use Lkt\Factory\Schemas\Schema;

final class MultipleFloatDataController
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

    /**
     * @param string $key
     * @return float[]|null
     */
    public function get(string $key): array|null
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

        $f = $this->schema->getFloatField($key);
        $mode = $f->getEmptyDataMode();

        if ($mode === EmptyDataMode::OnlyNull) return $v !== null;
        return $v > 0;
    }

    public function set(string $key, $value): self
    {
        $f = $this->schema->getFloatField($key);
        if (!$f) {
            throw InvalidItemDataAssignException::missingField($key);
        }

        $currentValue = $this->get($key);
        $parsedValue = $this->parse($key, $value);

        $diff = array_diff($currentValue, $parsedValue);
        if (count($diff) === 0) {
            $this->payload[$key] = $parsedValue;
        }

        return $this;
    }

    public function parse(string $key, $value): array|null
    {
        if ($value === null) return null;

        $f = $this->schema->getFloatField($key);
        $nullable = $f->isNullable();
        $minValue = $f->getMinValue();

        if (is_string($value)) {
            $value = explode(';', $value);
        }

        if (!is_array($value)) {
            if ($value) {
                $value = [$value];
            } else {
                $value = [];
            }
        }

        $r = [];
        foreach ($value as $item) {
            if (is_numeric($item)) {
                $r[] = (float)$item;
            } else {
                if ($nullable) $r[] = null;
                else $r[] = 0;
            }
        }
        if (is_float($minValue) && $minValue) {
            $r = array_filter($r, function ($item) use ($minValue) {
                return $item >= $minValue;
            });
        }

        return $r;

    }

    public function getOriginal(string $key): array|null
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

    public function __debugInfo() {
        return [
            'data' => $this->data,
            'payload' => $this->payload,
        ];
    }
}