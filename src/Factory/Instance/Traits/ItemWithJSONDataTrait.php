<?php

namespace Lkt\Factory\Instance\Traits;

use Lkt\Factory\Instance\DataControllers\FileDataController;
use Lkt\Factory\Instance\DataControllers\JsonDataController;
use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Schemas\Schema;

trait ItemWithJSONDataTrait
{
    protected JsonDataController $jsonData;

    private function initJSONData(Schema $schema, Item $item, array $rawData): static
    {
        $this->jsonData = new JsonDataController($schema, $item, $rawData);
        return $this;
    }
}