<?php

namespace Lkt\Factory\Instance\DataControllers;

use Lkt\Debug\VarDumper;
use Lkt\Factory\Instance\Enums\EmptyDataMode;
use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Instantiator\Validations\ParseColumn;
use Lkt\Factory\Schemas\Exceptions\InvalidItemDataAssignException;
use Lkt\Factory\Schemas\Schema;

final class JsonDataController
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

    public function get(string $key): array|\StdClass|null
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

        $f = $this->schema->getJSONField($key);
        $mode = $f->getEmptyDataMode();

        if ($mode === EmptyDataMode::OnlyNull) return $v !== null;
        if ($f->isAssoc()) return count($v) > 0;
        return count((array)$v);
    }

    public function set(string $key, $value): self
    {
        $f = $this->schema->getJSONField($key);
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

    public function parse(string $key, $value): array|\StdClass|null
    {
        if ($value === null) return null;

        $f = $this->schema->getJSONField($key);
        $associative = $f->isAssoc();

        if (is_array($value)) {
            if ($associative) return $value;
            return json_decode(json_encode($value), false);
        }

        if (is_object($value)) {
            if ($associative) return json_decode(json_encode($value), true);
            return $value;
        }

        if (is_string($value)){
            $value = htmlspecialchars_decode($value, JSON_UNESCAPED_UNICODE|ENT_QUOTES);
            $value = ParseColumn::HTMLDatumToInstance($value);
            return json_decode($value, $associative);
        }

        return json_decode('{}', $associative);
    }

    public function getOriginal(string $key): array|\StdClass|null
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