<?php

namespace Lkt\Factory\Instance\Traits;

use Lkt\Factory\Instance\DataControllers\MultipleFloatDataController;
use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Schemas\Schema;

trait ItemWithMultipleFloatDataTrait
{
    private MultipleFloatDataController $multipleFloatData;

    private function initMultipleFloatData(Schema $schema, Item $item, array $rawData): static
    {
        $this->multipleFloatData = new MultipleFloatDataController($schema, $item, $rawData);
        return $this;
    }
}