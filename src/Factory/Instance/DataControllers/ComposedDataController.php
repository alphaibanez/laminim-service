<?php

namespace Lkt\Factory\Instance\DataControllers;

use Lkt\Debug\VarDumper;
use Lkt\Factory\Instance\Enums\EmptyDataMode;
use Lkt\Factory\Instance\Enums\InvalidDataMode;
use Lkt\Factory\Instance\Enums\RetrieveDataMode;
use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Instantiator\ComponentId;
use Lkt\Factory\Instantiator\Enums\CrudOperation;
use Lkt\Factory\Instantiator\Instances\AbstractInstance;
use Lkt\Factory\Instantiator\Instantiator;
use Lkt\Factory\Schemas\Exceptions\InvalidItemDataAssignException;
use Lkt\Factory\Schemas\Fields\ForeignKeyField;
use Lkt\Factory\Schemas\Fields\RelatedField;
use Lkt\Factory\Schemas\Schema;
use function Lkt\Tools\Arrays\compareArrays;

final class ComposedDataController
{
    private array $data = [];
    private array $payload = [];
    private array $additionalData = [];

    private array $needsUpdate = [];

    private Schema $schema;
    private Item $item;

    public function __construct(Schema &$schema, Item &$ins)
    {
        $this->schema = $schema;
        $this->item = $ins;
    }

    public function getItem(string $key, array $additionalData = []): Item|null
    {
        return $this->item->retrieveValue($key, $additionalData, RetrieveDataMode::Item);
    }

    public function setItems(array $items): self
    {
        $this->needsUpdate = $items;
        return $this;
    }

    public function hasToSave(): bool
    {
        return count($this->needsUpdate) > 0;
    }

    public function save(): self
    {
        /**
         * @var string $key
         * @var Item $item
         */
        foreach ($this->needsUpdate as $key => $item) {

            $field = $this->schema->getCompositionFieldComposingThisField($key);

            if ($field instanceof RelatedField) {
                $relatedComponent = $field->getComponent($this->schema, $this->item);
                $relatedSchema = Schema::get($relatedComponent);

                $pointersToMe = $relatedSchema->getFieldsPointingToComponent($this->schema->getComponent());
                foreach ($pointersToMe as $pointerToMe) {
                    if (!$item->hasAssignedValue($pointerToMe->getName())) {
                        $item->assignValue($pointerToMe->getName(), $this->item->getIdColumnValue());
                    }
                }
            }

            $item->save();

            if ($field instanceof ForeignKeyField) {
                if (!$this->item->hasAssignedValue($field->getName())) {
                    $item->assignValue($field->getName(), $item->getIdColumnValue());
                }
            }
        }
        return $this;
    }

    public function has(string $key): bool
    {
        $v = $this->getItem($key);

        $f = $this->schema->getForeignKeyField($key);
        $mode = $f->getEmptyDataMode();

        if ($mode === EmptyDataMode::OnlyNull) return $v !== null;
        return $v > 0;
    }

    public function __debugInfo() {
        return [
            'data' => $this->data,
            'payload' => $this->payload,
            'needsUpdate' => $this->needsUpdate,
        ];
    }
}