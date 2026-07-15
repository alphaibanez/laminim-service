<?php

namespace Lkt\Factory\Instance\Traits;

use Lkt\Factory\Instance\DataControllers\FloatDataController;
use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Schemas\Schema;

trait ItemWithFloatDataTrait
{
    protected FloatDataController $floatData;

    private function initFloatData(Schema $schema, Item $item, array $rawData): static
    {
        $this->floatData = new FloatDataController($schema, $item, $rawData);
        return $this;
    }
}