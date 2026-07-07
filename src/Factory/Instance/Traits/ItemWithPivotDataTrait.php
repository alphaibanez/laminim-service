<?php

namespace Lkt\Factory\Instance\Traits;

use Lkt\Factory\Instance\DataControllers\PivotDataController;
use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Schemas\Schema;

trait ItemWithPivotDataTrait
{
    private PivotDataController $pivotData;

    private function initPivotData(Schema $schema, Item $item): static
    {
        $this->pivotData = new PivotDataController($schema, $item);
        return $this;
    }
}