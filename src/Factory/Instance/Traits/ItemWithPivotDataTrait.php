<?php

namespace Lkt\Factory\Instance\Traits;

use Lkt\Factory\Instance\DataControllers\PivotDataController;
use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Schemas\Schema;

trait ItemWithPivotDataTrait
{
    private PivotDataController $pivotData;

    private function initPivotData(Schema $schema, Item $item, bool $refreshing = false): static
    {
        if ($refreshing && isset($this->pivotData)) return $this;

        $this->pivotData = new PivotDataController($schema, $item);
        return $this;
    }
}