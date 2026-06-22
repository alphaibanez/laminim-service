<?php

namespace Lkt\Factory\Instance\DataControllers;

use Lkt\Factory\Instance\Enums\EmptyDataMode;
use Lkt\Factory\Instance\Enums\InvalidDataMode;
use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Instantiator\Instances\AbstractInstance;
use Lkt\Factory\Instantiator\Instantiator;
use Lkt\Factory\Schemas\Exceptions\InvalidItemDataAssignException;
use Lkt\Factory\Schemas\Schema;

final class ForeignKeysDataController
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

    /**
     * @return int[]
     */
    public function getIds(string $key): array
    {
        $field = $this->schema->getForeignKeysField($key);
        if (!$field) return [];

        $allowAnonymous = $field->anonymousAllowed();

        $items = $this->get($key);
        if ($items === null) return [];

        $items = explode(';', $items);
        $items = array_filter($items, function ($item) use ($allowAnonymous) {
            $t = trim($item);
            if ($t === '') {
                return false;
            }
            if ($allowAnonymous) {
                return true;
            }
            return (int)$t > 0;
        });

        return array_values($items);
    }

    /**
     * @param string $key
     * @return Item[]
     * @throws \Lkt\Factory\Schemas\Exceptions\InvalidComponentException
     * @throws \Lkt\Factory\Schemas\Exceptions\InvalidSchemaAppClassException
     * @throws \Lkt\Factory\Schemas\Exceptions\SchemaNotDefinedException
     */
    public function getItems(string $key): array
    {
        $field = $this->schema->getForeignKeysField($key);
        if (!$field) return [];

        $ids = $this->getIds($key);
        if (count($ids) === 0) return [];

        $relatedComponent = $field->getComponent();
        $dynamicComponentFieldName = $field->getDynamicComponentField();
        if ($dynamicComponentFieldName !== '') {
            $dynamicComponentField = $this->schema->getField($dynamicComponentFieldName);
            $getter = $dynamicComponentField->getGetterForPrimitiveValue();
            $dynamicType = $this->item->{$getter}();
            if ($dynamicType !== '') $relatedComponent = $dynamicType;
        }

        if ($relatedComponent === '') return [];


        $relatedSchema = Schema::get($relatedComponent);
        $relatedClass = $relatedSchema->getInstanceSettings()->getAppClass();

        $identifiers = $relatedSchema->getIdentifiers();
        $identifiers = reset($identifiers);
        $idColumn = $identifiers?->getName() ?? '';

        $r = [];
        foreach ($ids as $id) {
            if (is_numeric($id)) {
                if ($relatedClass) {
                    $instance = call_user_func_array([$relatedClass, 'getInstance'], ['id' => $id]);
                    if ($instance instanceof AbstractInstance && !$instance->isAnonymous()) {
                        $r[] = $instance;
                    }

                } else {
                    $t = Instantiator::make($relatedComponent, $id);
                    if ($t instanceof AbstractInstance && !$t->isAnonymous()) {
                        $r[] = $t;
                    }
                }
            } else {
                $t = Instantiator::make($relatedComponent, null);
                $t->setData([
                    $idColumn => $id,
                ]);
                $r[] = $t;
            }
        }
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

        if (!is_array($value) && !is_string($value)) {
            $value = trim($value);
        }

        $parsed = explode(';', $value);

        $identifiers = $this->schema->getIdentifiers();
        $instanceIdentifierValue = $this->item->getIdentifierValue();
        $itemId = null;
        if (count($identifiers) === 1) {
            $itemId = $instanceIdentifierValue[$identifiers[0]->getName()];
        }

        if (is_array($parsed)) {
            $index = array_search($itemId, $parsed);
            if ($index !== false) {
                unset($parsed[$index]);
            }
        }

        $value = implode(';', $parsed);


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

    public function __debugInfo() {
        return [
            'data' => $this->data,
            'payload' => $this->payload,
        ];
    }
}