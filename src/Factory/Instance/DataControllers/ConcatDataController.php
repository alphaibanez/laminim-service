<?php

namespace Lkt\Factory\Instance\DataControllers;

use Lkt\Debug\VarDumper;
use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Schemas\Schema;

final class ConcatDataController
{
    private array $data = [];

    private Schema $schema;
    private Item $item;

    public function __construct(Schema &$schema, Item &$ins)
    {
        $this->schema = $schema;
        $this->item = $ins;
    }

    public function get(string $key): string
    {
        $field = $this->schema->getConcatField($key);
        $r = [];
        foreach ($field->getConcatenatedFields() as $concatenatedField) {
            $r[] = $this->item->retrieveValue($concatenatedField);
        }
        return trim(implode($field->getSeparator(), $r));
    }

    public function has(string $key): bool
    {
        return $this->get($key) !== '';
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