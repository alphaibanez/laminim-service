<?php

namespace Lkt\Factory\Instance\Traits;

use Lkt\Factory\Instance\DataControllers\BooleanDataController;
use Lkt\Factory\Instance\DataControllers\ConstantDataController;
use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Schemas\Schema;

trait ItemWithConstantDataTrait
{
    private ConstantDataController $constantData;

    private function initConstantData(Schema $schema, Item $item): static
    {
        $this->constantData = new ConstantDataController($schema, $item);
        return $this;
    }
}