<?php

namespace Lkt\Factory\Instance\Traits;

use Lkt\Factory\Instance\DataControllers\ConstantDataController;
use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Schemas\Schema;

trait ItemWithConstantDataTrait
{
    protected ConstantDataController $constantD;

    protected function initConstantData(Schema $schema, Item $item): static
    {
        $this->constantD = new ConstantDataController($schema, $item);
        return $this;
    }
}