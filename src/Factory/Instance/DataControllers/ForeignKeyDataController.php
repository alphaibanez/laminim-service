<?php

namespace Lkt\Factory\Instance\DataControllers;

use Lkt\Debug\VarDumper;
use Lkt\Factory\Instance\Enums\EmptyDataMode;
use Lkt\Factory\Instance\Enums\InvalidDataMode;
use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Instantiator\ComponentId;
use Lkt\Factory\Instantiator\Instantiator;
use Lkt\Factory\Schemas\Exceptions\InvalidItemDataAssignException;
use Lkt\Factory\Schemas\Schema;

final class ForeignKeyDataController
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

    public function getItem(string $key): Item|null
    {
        $field = $this->schema->getForeignKeyField($key);
        if (!$field) return null;

        $type = $field->getComponent();
        $dynamicComponentFieldName = $field->getDynamicComponentField();
        if ($dynamicComponentFieldName !== '') {
            $dynamicComponentField = $this->schema->getField($dynamicComponentFieldName);
            $getter = $dynamicComponentField->getGetterForPrimitiveValue();
            $dynamicType = $this->item->{$getter}();
            if (is_numeric($dynamicType)) $type = ComponentId::getComponent((int)$dynamicType);
            elseif ($dynamicType !== '') $type = $dynamicType;
        }
        $id = $this->get($key);

        if (!$type || $id <= 0) {
            return null;
        }
        return Instantiator::make($type, $id);
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