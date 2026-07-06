<?php

namespace Lkt\Factory\Instance\DataControllers;

use Lkt\Debug\VarDumper;
use Lkt\Factory\Instance\Enums\EmptyDataMode;
use Lkt\Factory\Instance\Enums\InvalidDataMode;
use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Instantiator\Instances\AbstractInstance;
use Lkt\Factory\Instantiator\Instantiator;
use Lkt\Factory\Schemas\Exceptions\InvalidItemDataAssignException;
use Lkt\Factory\Schemas\Fields\ForeignKeyField;
use Lkt\Factory\Schemas\Schema;
use function Lkt\Tools\Arrays\compareArrays;

final class ForeignKeysDataController
{
    private array $data = [];
    private array $payload = [];
    private array $needsUpdate = [];

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
        $items = array_map(function ($item) use ($allowAnonymous) {
            return (int)$item;
        }, $items);

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

        $relatedComponent = $field->getComponent($this->schema, $this->item);
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

        return $r;
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
        $f = $this->schema->getForeignKeysField($key);
        if (!$f) {
            throw InvalidItemDataAssignException::missingField($key);
        }

        $identifiers = $this->schema->getIdentifiers();
        $instanceIdentifierValue = $this->item->getIdentifierValue();
        $itemId = null;
        if (count($identifiers) === 1) {
            $itemId = $instanceIdentifierValue[$identifiers[0]->getName()];
        }

        $parsed = $value;
        if (!is_array($value)) {
            if (!is_string($value)) {
                $value = trim($value);
                $parsed = explode(';', $value);
            } elseif (is_string($value)) {
                $parsed = explode(';', $value);
            }
        }

        if (is_array($parsed)) {
            $index = array_search($itemId, $parsed);
            if ($index !== false) {
                unset($parsed[$index]);
            }
        }


        if (is_array($parsed)) {
            if (is_numeric($parsed[0])) {
                $value = implode(';', $parsed);

                $currentValue = $this->get($key);
                $parsedValue = $this->parse($key, $value);

                if ($parsedValue !== $currentValue) {
                    $this->payload[$key] = $parsedValue;
                }
            } else {
                $this->needsUpdate[$key] = $parsed;
            }
        }

        return $this;
    }

    public function parse(string $key, $value): string|null
    {
        if ($value === null) return null;

        $f = $this->schema->getForeignKeysField($key);
        if (!$f) {
            throw InvalidItemDataAssignException::missingField($key);
        }

        if (is_int($value)) {
            return (string)$value;

        } elseif (is_string($value)) {
            return trim($value);
        }

        return null;
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

    public function save(): self
    {
        /**
         * @var string $key
         * @var array[] $items
         */
        foreach ($this->needsUpdate as $key => $items) {

            $field = $this->schema->getForeignKeysField($key);

            $relatedComponent = $field->getComponent($this->schema, $this->item);
            $relatedSchema = Schema::get($relatedComponent);
            /** @var AbstractInstance $relatedClass */
            $relatedClass = $relatedSchema->getInstanceSettings()->getAppClass();

            $currentIds = $this->getIds($key);
            $updatedIds = [];

            $updatedInstances = [];

            foreach ($items as $item) {
                /** @var Item $ins */
                $ins = is_array($item) ? $relatedClass::getInstance($item) : $item;
                $ins->feed($item);
                $updatedInstances[] = $ins;
                $updatedIds[] = $ins->getIdColumnValue();
            }

            if (count($updatedInstances) > 0) {
                $batchActions = $relatedClass::getBatchActions($updatedInstances);
                $batchActions->update();
            }

            $diff = compareArrays($currentIds, $updatedIds);

            // Delete
            if (count($diff['deleted']) > 0 && method_exists($field, 'hasToAutoRemoveUnlinked') && $field->hasToAutoRemoveUnlinked()) {
                foreach ($diff['deleted'] as $deletedId) {
                    $ins = $relatedClass::getInstance($relatedSchema->decodeInstanceCode($deletedId));
                    $ins->delete();
                }
            }

            $this->set($key, implode(';', $updatedIds));
        }

        return $this;
    }

    public function hasToSave(): bool
    {
        return count($this->needsUpdate) > 0;
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
            'needsUpdate' => $this->needsUpdate,
        ];
    }
}