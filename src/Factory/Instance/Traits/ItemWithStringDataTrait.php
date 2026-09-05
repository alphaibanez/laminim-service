<?php

namespace Lkt\Factory\Instance\Traits;

use Lkt\Factory\Instance\DataControllers\StringDataController;
use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Schemas\Schema;

trait ItemWithStringDataTrait
{
    protected StringDataController $stringData;

    protected function initStringData(Schema $schema, Item $item, array $rawData): static
    {
        $this->stringData = new StringDataController($schema, $item, $rawData);
        return $this;
    }
}