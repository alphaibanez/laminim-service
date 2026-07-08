<?php

namespace Lkt\Factory\Instance\Traits;

use Lkt\Factory\Instance\DataControllers\ComposedDataController;
use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Schemas\Schema;

trait ItemWithComposedDataTrait
{
    private ComposedDataController $composedData;

    private function initComposedData(Schema $schema, Item $item, bool $refreshing = false): static
    {
        if ($refreshing && isset($this->pivotData)) return $this;

        $this->composedData = new ComposedDataController($schema, $item);
        return $this;
    }
}