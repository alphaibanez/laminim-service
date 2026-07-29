<?php

namespace Lkt\Factory\Instance\DataControllers;

use Lkt\Factory\Instance\Enums\EmptyDataMode;
use Lkt\Factory\Instance\Enums\InvalidDataMode;
use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Instantiator\Instantiator;
use Lkt\Factory\Schemas\Exceptions\InvalidItemDataAssignException;
use Lkt\Factory\Schemas\Schema;

final class ForeignKeyDataController
{
    private array $data = [];
    private array $payload = [];
    private array $items = [];

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

    public function getItem(string $key, array $additionalData = [], bool $retrieveAnonymous = false): Item|null
    {
        $field = $this->schema->getForeignKeyField($key);
        if (!$field) return null;

        if (array_key_exists($key, $this->items)) return $this->items[$key];

        $type = $field->getComponent($this->schema, $this->item);
        $id = $this->get($key);

        $relatedComponent = $field->getComponent($this->schema, $this->item);
        $relatedSchema = Schema::get($relatedComponent);

        if (!$type || $id <= 0) {
            if ($retrieveAnonymous) {
                $instance = $relatedSchema->getItemInstance();
                $instance->feed($additionalData);
                $this->items[$key] = $instance;
                return $this->items[$key];
            }
            return null;
        }

        if (count($additionalData) > 0) {
            $query = $relatedSchema->getQueryBuilder();
            $relatedSchema->filterBuilder($query, $additionalData);
            if ($query->hasConstraints()) {
                $instance = $relatedSchema->getOne($query);

                if (!$instance || $instance->isAnonymous()) {
                    $instance = $relatedSchema->getItemInstance();
                    $instance->feed($additionalData);
                }

                $this->items[$key] = $instance;
                return $this->items[$key];
            }
        }

        $instance = $relatedSchema->getItemInstance($id);
        if ($instance->isAnonymous() && count($additionalData) > 0) {
            $instance->feed($additionalData);
        }

        $this->items[$key] = $instance;
        return $this->items[$key];
    }

    public function has(string $key): bool
    {
        $v = $this->get($key);

        $f = $this->schema->getForeignKeyField($key);
        $mode = $f->getEmptyDataMode();

        if ($mode === EmptyDataMode::OnlyNull) return $v !== null;
        return $v > 0;
    }

    public function set(string $key, $value): self
    {
        $f = $this->schema->getForeignKeyField($key);
        if (!$f) {
            throw InvalidItemDataAssignException::missingField($key);
        }

        $currentValue = $this->get($key);
        $parsedValue = null;

        if (is_array($value)) {
            $relatedSchema = Schema::get($f->getComponent($this->schema, $this->item));
            $relatedIdFields = $relatedSchema->getIdentifiers();
            if (count($relatedIdFields) === 1) {
                $relatedIdKey = $relatedIdFields[0]->getName();
                $relatedId = isset($value[$relatedIdKey]) ? (int)$value[$relatedIdKey] : 0;
                if ($relatedId > 0) {
                    $parsedValue = $relatedId;
                }
            }

        } else {
            $parsedValue = $this->parse($key, $value);
        }

        if ($parsedValue !== $currentValue) {
            $this->payload[$key] = $parsedValue;
        }

        return $this;
    }

    public function parse(string $key, $value): int|null
    {
        if ($value === null) return null;

        $f = $this->schema->getForeignKeyField($key);

        if (is_int($value)) {
            return $value;

        } else {
            $mode = $f->getInvalidDataMode();

            $value = match ($mode) {
                InvalidDataMode::CastToType => (int)$value,
                InvalidDataMode::CastToEmpty => 0,
                default => null,
            };
        }

        return $value;
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