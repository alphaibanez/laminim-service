<?php

namespace Lkt\Factory\Instance\Traits;

use Lkt\Factory\Instance\DataControllers\BooleanDataController;
use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Schemas\Schema;

trait ItemWithBooleanDataTrait
{
    protected BooleanDataController $booleanD;

    protected function initBooleanData(Schema $schema, Item $item, array $rawData): static
    {
        $this->booleanD = new BooleanDataController($schema, $item, $rawData);
        return $this;
    }
}