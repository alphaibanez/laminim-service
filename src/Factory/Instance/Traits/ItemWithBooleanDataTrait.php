<?php

namespace Lkt\Factory\Instance\Traits;

use Lkt\Factory\Instance\DataControllers\BooleanDataController;
use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Schemas\Schema;

trait ItemWithBooleanDataTrait
{
    private BooleanDataController $booleanData;

    private function initBooleanData(Schema $schema, Item $item, array $rawData): static
    {
        $this->booleanData = new BooleanDataController($schema, $item, $rawData);
        return $this;
    }
}