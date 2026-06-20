<?php

namespace Lkt\Factory\Instance\Traits;

use Lkt\Factory\Instance\DataControllers\IntegerDataController;
use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Schemas\Schema;

trait WithIntegerDataTrait
{
    private IntegerDataController $integerData;

    private function initIntegerData(Schema $schema, Item $item, array $rawData): static
    {
        $this->integerData = new IntegerDataController($schema, $item, $rawData);
        return $this;
    }
}