<?php

namespace Lkt\Factory\Instance\Traits;

use Lkt\Factory\Instance\DataControllers\ConcatDataController;
use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Schemas\Schema;

trait ItemWithConcatDataTrait
{
    protected ConcatDataController $concatData;

    private function initConcatData(Schema $schema, Item $item): static
    {
        $this->concatData = new ConcatDataController($schema, $item);
        return $this;
    }
}