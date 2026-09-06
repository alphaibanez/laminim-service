<?php

namespace Lkt\Factory\Instance\DataControllers;

use Carbon\Carbon;
use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Schemas\Exceptions\InvalidItemDataAssignException;
use Lkt\Factory\Schemas\Schema;

final class DateDataController
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

    public function get(string $key): Carbon|null
    {
        if (array_key_exists($key, $this->payload)) {
            return $this->payload[$key];
        }

        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }

        return null;
    }

    public function format(string $key, string|null $format = null): string|null
    {
        if (!$this->has($key)) return null;

        if (!$format) $format = 'Y-m-d';

        $r = $this->get($key)?->format($format);
        if (!$r || str_starts_with($r, '-')) return '';
        return $r;
    }

    public function intlFormat(string $key, string|null $format = null): string|null
    {
        if (!$this->has($key)) return null;
        return \IntlDateFormatter::formatObject($this->get($key), $format);
    }

    public function has(string $key): bool
    {
        $v = $this->get($key);
        return $v !== null;
    }

    public function set(string $key, $value): self
    {
        $f = $this->schema->getDateTimeField($key);
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

    public function parse(string $key, $value): Carbon|null
    {
        if ($value === null) return null;

        if ($value instanceof Carbon) return $value;
        if ($value instanceof \DateTime) {
            return new Carbon(date('Y-m-d H:i:s', $value->getTimestamp()));
        }

        if (is_string($value)) {
            $value = trim($value);
            if ($value === '') return null;
            if ($value === '0000-00-00 00:00:00') return null;
            $value = strtotime($value);
        }

        $str = date('Y-m-d H:i:s', (int)$value);

        try {
            return new Carbon($str);

        } catch (\Exception $e) {
            return null;
        }
    }

    public function getOriginal(string $key): Carbon|null
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