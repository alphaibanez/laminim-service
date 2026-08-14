<?php

namespace Lkt\Factory\Instance\Traits;

use Lkt\Factory\Instance\DataControllers\ComposedDataController;
use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Schemas\Schema;

trait ItemWithComposedDataTrait
{
    protected ComposedDataController $composedData;

    private function initComposedData(Schema $schema, Item $item, bool $refreshing = false): static
    {
        if ($refreshing && isset($this->pivotData)) return $this;

        $this->composedData = new ComposedDataController($schema, $item);
        return $this;
    }

    protected function getCompositionInstance(string $composedComponent, array $additionalData = []): mixed
    {
        return $this->composedData->getItem($composedComponent, $additionalData);
    }

    protected function getCompositionVal(string $composedComponent, string $fieldName, array $additionalData = []): mixed
    {
        return $this->composedData->get($composedComponent, $fieldName, $additionalData);
    }

    protected function setCompositionVal(string $composedComponent, string $fieldName, mixed $value, array $additionalData = []): static
    {
        $this->composedData->set($composedComponent, $fieldName, $value, $additionalData);
        return $this;
    }

    protected function hasCompositionVal(string $composedComponent, string $fieldName, array $additionalData = []): bool
    {
        return $this->composedData->has($composedComponent, $fieldName, $additionalData);
    }
}