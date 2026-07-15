<?php

namespace Lkt\Factory\Instance\DataControllers;

use Lkt\Factory\Instance\Enums\EmptyDataMode;
use Lkt\Factory\Instance\Enums\RetrieveDataMode;
use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Schemas\Fields\AbstractField;
use Lkt\Factory\Schemas\Fields\ForeignKeyField;
use Lkt\Factory\Schemas\Fields\RelatedField;
use Lkt\Factory\Schemas\Schema;

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

    public function hasComposedInstance(string $key): bool
    {
        return isset($this->needsUpdate[$key]);
    }

    public function setComposedInstance(string $key, Item $item): self
    {
        $this->needsUpdate[$key] = $item;
        return $this;
    }

    public function getComposedInstance(string $key): Item
    {
        return $this->needsUpdate[$key];
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

    public function prepareAdditionalData(string $key, array $additionalData = []): array
    {
        $compositionValuesFields = $key ? $this->schema->getCompositionValueFields($key) : $this->schema->getAllCompositionValueFields();
        /**
         * @var  $key
         * @var AbstractField $compositionValueField
         */
        foreach ($compositionValuesFields as $key => $compositionValueField) {
            if (!$additionalData[$key]) {
                if ($compositionValueField instanceof ForeignKeyField) {
                    $getterAux = $compositionValueField->getGetterForData();
                } else {
                    $getterAux = $compositionValueField->getGetterForPrimitiveValue();
                }

                if (is_callable([$this, $getterAux])) {
                    $additionalData[$key] = $this->{$getterAux}();
                }
            }
        }

        return $additionalData;
    }

    public function __debugInfo() {
        return [
            'data' => $this->data,
            'payload' => $this->payload,
            'needsUpdate' => $this->needsUpdate,
        ];
    }
}