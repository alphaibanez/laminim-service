<?php

namespace Lkt\Factory\Instance\DataControllers;

use Lkt\Factory\Instance\Enums\EmptyDataMode;
use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Instantiator\Exceptions\InvalidIntegerChoiceValueException;
use Lkt\Factory\Schemas\Exceptions\InvalidItemDataAssignException;
use Lkt\Factory\Schemas\Fields\ValueListField;
use Lkt\Factory\Schemas\Schema;

final class MultipleStringDataController
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
     * @return string[]|null
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

    public function getAsString(string $key): string|null
    {
        $v = $this->get($key);
        if ($v === null) return null;

        $separator = ';';
        $field = $this->schema->getStringField($key);
        if ($field instanceof ValueListField) $separator = $field->getSeparator();

        return implode($separator, $v);
    }

    public function has(string $key): bool
    {
        $v = $this->get($key);

        $f = $this->schema->getStringField($key);
        $mode = $f->getEmptyDataMode();

        if ($mode === EmptyDataMode::OnlyNull) return $v !== null;
        return $v > 0;
    }

    public function set(string $key, $value): self
    {
        $f = $this->schema->getStringField($key);
        if (!$f) {
            throw InvalidItemDataAssignException::missingField($key);
        }

        $currentValue = $this->get($key);
        $parsedValue = $this->parse($key, $value);

        if ($parsedValue === null) {
            if ($parsedValue !== $currentValue) {
                $this->payload[$key] = $parsedValue;
            }
            return $this;
        }

        $diff = array_diff($currentValue ?? [], $parsedValue);
        if (count($diff) === 0) {
            $this->payload[$key] = $parsedValue;
        }

        return $this;
    }

    public function parse(string $key, $value): array|null
    {
        if ($value === null) return null;

        $f = $this->schema->getStringField($key);
        $nullable = $f->isNullable();

        $separator = ';';
        if ($f instanceof ValueListField) $separator = $f->getSeparator();

        if (is_string($value)) {
            $value = explode($separator, $value);
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
            if (is_string($item)) {
                $r[] = $item;
            } else {
                if ($nullable) $r[] = null;
                else $r[] = '';
            }
        }

        if ($f->ableToChoose()) {
            $availableOptions = $f->getAllowedOptions();

            foreach ($value as $val) {

                $v = $val;
                if (is_object($v) && isset($v->value)) {
                    $v = $v->value;
                }

                if (is_string($v)) $v = (int)$v;

                if (!in_array($v, $availableOptions, true)) {
                    throw InvalidIntegerChoiceValueException::getInstance($v, $key, $this->schema->getComponent());
                }
            }
        }

        return $r;
    }

    public function in(string $key, array $values): bool
    {
        $value = $this->get($key);
        if (count($value) === 0) return false;

        $r = true;
        foreach ($value as $val) {
            $r = $r && in_array($val, $values, true);
        }

        return $r;
    }

    public function equal(string $key, array $compared): bool
    {
        $value = $this->get($key);

        $comparedValues = array_map(function ($v){
            $c = $v;
            if (is_object($v) && property_exists($v, 'value') && isset($v->value)) {
                $c = $v->value;
            }
            return $c;

        }, $compared);

        return count($value) === count($comparedValues)
            && count(array_intersect($value, $comparedValues)) === 0;
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