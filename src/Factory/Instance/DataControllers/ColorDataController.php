<?php

namespace Lkt\Factory\Instance\DataControllers;

use Lkt\Factory\Instance\Enums\EmptyDataMode;
use Lkt\Factory\Instance\Enums\InvalidDataMode;
use Lkt\Factory\Instance\Enums\TrimMode;
use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Schemas\Exceptions\DuplicatedValueException;
use Lkt\Factory\Schemas\Exceptions\InvalidItemDataAssignException;
use Lkt\Factory\Schemas\Schema;
use function Lkt\Tools\Color\decToHex;
use function Lkt\Tools\Color\hexToDec;

final class ColorDataController
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

    public function getRGBA(string $key, float $opacity = null): null|array
    {
        $r = $this->get($key);
        if (!$r) return null;

        $r = hexToDec($r);
        if ($opacity !== null) {
            $r[] = $opacity;
        }

        return $r;
    }

    public function getRGBAString(string $key, float $opacity = null): string
    {
        $color = $this->getRGBA($key, $opacity);
        if ($color === null) return '';
        $base = 'rgb';
        if (count($color) === 4) {
            $base .= 'a';
        }

        $r = implode(',', $color);
        return "{$base}($r)";
    }

    public function has(string $key): bool
    {
        $v = $this->get($key);

        $f = $this->schema->getColorField($key);
        $mode = $f->getEmptyDataMode();

        if ($mode === EmptyDataMode::OnlyNull) return $v !== null;
        return $v !== '';
    }

    public function set(string $key, $value): self
    {
        $f = $this->schema->getColorField($key);
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

    public function parse(string $key, $value): string|null
    {
        if ($value === null) return null;

        $f = $this->schema->getColorField($key);

        if (is_array($value)) {
            $value = decToHex($value);
        }

        if (is_string($value)) {
            return $this->trim($value, TrimMode::Full);
        }

        $mode = $f->getInvalidDataMode();

        return match ($mode) {
            InvalidDataMode::CastToType => $this->trim((string)$value, TrimMode::Full),
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