<?php

namespace Lkt\Factory\Instance\Traits;

use Lkt\Factory\Instance\DataControllers\BooleanDataController;
use Lkt\Factory\Instance\DataControllers\ColorDataController;
use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Schemas\Schema;

trait ItemWithColorDataTrait
{
    private ColorDataController $colorData;

    private function initColorData(Schema $schema, Item $item, array $rawData): static
    {
        $this->colorData = new ColorDataController($schema, $item, $rawData);
        return $this;
    }
}