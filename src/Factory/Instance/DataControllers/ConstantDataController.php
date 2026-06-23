<?php

namespace Lkt\Factory\Instance\DataControllers;

use Lkt\Factory\Instance\Enums\EmptyDataMode;
use Lkt\Factory\Instance\Enums\InvalidDataMode;
use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Schemas\Exceptions\InvalidItemDataAssignException;
use Lkt\Factory\Schemas\Fields\ConstantValueField;
use Lkt\Factory\Schemas\Schema;

final class ConstantDataController
{
    private array $data = [];

    private Schema $schema;
    private Item $item;

    public function __construct(Schema &$schema, Item &$ins)
    {
        $this->schema = $schema;
        $this->item = $ins;
    }

    public function get(string $key): mixed
    {
        $field = $this->schema->getConstantField($key);
        return $field?->getConstantValue();
    }

    public function has(string $key): bool
    {
        $field = $this->schema->getConstantField($key);
        if (!$field) return false;
        return true;
    }

    public function setOriginal(string $key, $value): self
    {
        $this->data[$key] = $value;
        return $this;
    }

    public function __debugInfo() {
        return [
            'data' => $this->data,
        ];
    }
}